<?php

namespace ZapsterStudios\Ally\Notifications;

use Ally;
use App\User;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerification extends Notification
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
            ->subject('Email Verification')
            ->greeting('Hi, '.$this->user->name.'!')
            ->line('You are receiving this email because you recently registered on **'.config('app.name').'**. To continue using your account you must verify your account by clicking on the button below.')
            ->action('Verify Account', str_replace('{token}', $this->token, Ally::$linkAccountVerification))
            ->line('If you did not create an account please contact our support.');
    }
}
