<?php

namespace ZapsterStudios\TeamPay\Policies;

use App\User;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Access\HandlesAuthorization;

class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the notification.
     *
     * @param  \App\User  $user
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return mixed
     */
    public function view(User $user, Notification $notification = null)
    {
        return $user->tokenCan('notifications.show')
            && (! $notification || (
                $notification->notifiable_id === $user->id
                && $notification->notifiable_type == 'App\User'
            ));
    }

    /**
     * Determine whether the user can update the notification.
     *
     * @param  \App\User  $user
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return mixed
     */
    public function update(User $user, Notification $notification)
    {
        return $user->tokenCan('notifications.update')
            && $notification->notifiable_id === $user->id
            && $notification->notifiable_type == 'App\User';
    }

    /**
     * Determine whether the user can delete the notification.
     *
     * @param  \App\User  $user
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return mixed
     */
    public function delete(User $user, Notification $notification)
    {
        return $user->tokenCan('notifications.delete')
            && $notification->notifiable_id === $user->id
            && $notification->notifiable_type == 'App\User';
    }
}
