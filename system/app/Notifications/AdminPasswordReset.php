<?php

namespace App\Notifications;

use App\Services\Settings;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Password reset mail for admin panel accounts. Laravel's built-in notification
 * points at the "password.reset" route, which belongs to the public site — this
 * one links to the admin panel instead.
 */
class AdminPasswordReset extends Notification
{
    public function __construct(public string $token) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $minutes = config('auth.passwords.admins.expire', 60);
        $agency = app(Settings::class)->get('agency_name', config('app.name'));

        $url = route('admin.password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        return (new MailMessage)
            ->subject($agency.' — Reset your admin password')
            ->greeting('Hello '.($notifiable->name ?? '').'!')
            ->line('We received a request to reset the password for your '.$agency.' admin account.')
            ->action('Reset Password', $url)
            ->line('This link expires in '.$minutes.' minutes.')
            ->line('If you did not request a password reset, no further action is required.')
            ->salutation('— '.$agency);
    }
}
