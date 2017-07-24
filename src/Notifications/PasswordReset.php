<?php

namespace ZapsterStudios\Ally\Notifications;

use Ally;
use App\User;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordReset extends Notification
{
    /**
     * Create a new notification instance.
     *
     * @param  \App\User  $user
     * @param  string  $token
     * @return void
     */
    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Password Reset')
            ->greeting('Hi, '.$this->user->name.'!')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', str_replace('{token}', $this->token, Ally::$linkPasswordReset))
            ->line('If you did not request a password reset, no further action is required.');
    }
}
