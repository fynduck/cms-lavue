<?php

namespace Modules\User\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line(trans('passwords.received_password_reset'))
            ->action(trans('passwords.reset_password'), $this->resetUrl($notifiable))
            ->line(trans('passwords.you_did_not_request_password_reset'));
    }

    /**
     * Get the reset password URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function resetUrl($notifiable)
    {
        $appUrl = config('app.client_url', config('app.url'));

        return url("$appUrl/password/reset/$this->token") . '?email=' . urlencode($notifiable->email);
    }
}
