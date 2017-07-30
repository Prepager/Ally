<?php

namespace ZapsterStudios\Ally\Tests\Stubs;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class DatabaseNotification extends Notification
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
            'is_this_a_test' => true,
        ];
    }
}
