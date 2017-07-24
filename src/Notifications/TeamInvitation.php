<?php

namespace ZapsterStudios\Ally\Notifications;

use Ally;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TeamInvitation extends Notification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($team, $user, $exists)
    {
        $this->team = $team;
        $this->user = $user;
        $this->exists = $exists;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $notifiable->exists ? ['mail', 'database'] : ['mail'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toArray($notifiable)
    {
        return [
            'team_name' => $this->team->name,
            'user_name' => $this->user->name,
        ];
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
            ->subject(ucfirst(Ally::$teamName).' Invitation')
            ->greeting($this->exists ? 'Hi, '.$this->user->name.'!' : 'Hi!')
            ->line('You are receiving this email because the owner of **'.$this->team->name.'** invited you to become part of their '.Ally::$teamName.' on **'.config('app.name').'**.')
            ->action('View Invitations', Ally::$linkInvitations)
            ->line($this->exists
                ? 'Since you already have an account you can accept or decline the invite by clicking on the button above and logging in.'
                : 'To accept the invite you must register a new account on **'.config('app.name').'**. If you are however not interested in joining the '.Ally::$teamName.' you can simply ignore this email.'
            );
    }
}
