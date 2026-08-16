<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected string $verificationUrl
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verify Your Email')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Thank you for registering.')
            ->line('Please click the button below to verify your email address.')
            ->action('Verify Email', $this->verificationUrl)
            ->line('This verification link will expire in 60 minutes.');
    }
}