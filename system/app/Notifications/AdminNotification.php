<?php

namespace App\Notifications;

use App\Services\MailConfigurator;
use App\Services\Settings;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Base for every in-panel notification. Subclasses describe themselves through
 * payload() and the bell/notification screen renders that consistently.
 *
 * The panel record is always written. A copy is emailed as well when
 * Settings → Email has both a working mailer and "Email Panel Notifications"
 * switched on.
 */
abstract class AdminNotification extends Notification
{
    use Queueable;

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        if (! empty($notifiable->email) && app(MailConfigurator::class)->emailNotificationsEnabled()) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /** @return array{title: string, message: string, icon: string, url: string|null} */
    abstract protected function payload(): array;

    /** @return array<string, mixed> */
    public function toDatabase(object $notifiable): array
    {
        return array_merge([
            'title' => 'Notification',
            'message' => '',
            'icon' => 'fa-circle-info',
            'url' => null,
        ], $this->payload());
    }

    /** The emailed copy says exactly what the bell says, plus a link back. */
    public function toMail(object $notifiable): MailMessage
    {
        $payload = $this->toDatabase($notifiable);
        $agency = app(Settings::class)->get('agency_name', config('app.name'));

        $mail = (new MailMessage)
            ->subject($agency.' — '.$payload['title'])
            ->greeting('Hello '.($notifiable->name ?? '').'!')
            ->line($payload['message']);

        if (! empty($payload['url'])) {
            $mail->action('Open in Panel', $payload['url']);
        }

        return $mail->salutation('— '.$agency.' Admin Panel');
    }
}
