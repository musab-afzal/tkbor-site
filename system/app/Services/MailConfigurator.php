<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Lets the SMTP account be managed from Settings → Email instead of .env, so a
 * non-developer can switch mail servers without touching the filesystem.
 * The stored password is encrypted at rest.
 */
class MailConfigurator
{
    public function __construct(protected Settings $settings) {}

    /** Push the stored SMTP account into the runtime mail configuration. */
    public function apply(): void
    {
        if (! $this->settings->get('mail_enabled')) {
            return;
        }

        $host = $this->settings->get('mail_host');

        if (! $host) {
            return;
        }

        $encryption = $this->settings->get('mail_encryption', 'tls');

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $host);
        Config::set('mail.mailers.smtp.port', (int) $this->settings->get('mail_port', 587));
        Config::set('mail.mailers.smtp.username', $this->settings->get('mail_username'));
        Config::set('mail.mailers.smtp.password', $this->password());
        Config::set('mail.mailers.smtp.scheme', $encryption === 'ssl' ? 'smtps' : 'smtp');
        Config::set('mail.mailers.smtp.verify_peer', (bool) $this->settings->get('mail_verify_peer', true));

        if ($from = $this->settings->get('mail_from_address')) {
            Config::set('mail.from.address', $from);
        }

        Config::set('mail.from.name', $this->settings->get(
            'mail_from_name',
            $this->settings->get('agency_name', config('app.name'))
        ));
    }

    /** True when mail can actually leave the building. */
    public function isConfigured(): bool
    {
        if ($this->settings->get('mail_enabled') && $this->settings->get('mail_host')) {
            return true;
        }

        return ! in_array(config('mail.default'), ['log', 'array', null], true);
    }

    /** Should panel notifications also be emailed? */
    public function emailNotificationsEnabled(): bool
    {
        return (bool) $this->settings->get('mail_notifications') && $this->isConfigured();
    }

    /** Decrypt the stored SMTP password, tolerating a value saved in plain text. */
    public function password(): ?string
    {
        $stored = $this->settings->get('mail_password');

        if (! $stored) {
            return null;
        }

        try {
            return Crypt::decryptString($stored);
        } catch (DecryptException) {
            return $stored;
        }
    }

    public function encrypt(string $plain): string
    {
        return Crypt::encryptString($plain);
    }

    /**
     * Send anything without ever letting a mail failure break the request that
     * triggered it — the panel notification has already been stored.
     */
    public function safely(callable $callback): bool
    {
        if (! $this->isConfigured()) {
            return false;
        }

        try {
            $callback();

            return true;
        } catch (\Throwable $e) {
            Log::warning('Mail delivery failed: '.$e->getMessage());

            return false;
        }
    }

    /** @throws \Throwable */
    public function sendTest(string $recipient): void
    {
        $this->apply();

        $agency = $this->settings->get('agency_name', config('app.name'));

        Mail::raw(
            "This is a test message from your {$agency} admin panel.\n\n".
            "If you can read this, outgoing email is configured correctly.\n\n".
            'Sent at '.now()->toDayDateTimeString(),
            fn ($message) => $message->to($recipient)->subject($agency.' — Test Email')
        );
    }
}
