<?php

namespace ZapsterStudios\Ally\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Notifications\DatabaseNotification;

class NotificationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the notification.
     *
     * @param  \App\User  $user
     * @param  \Illuminate\Notifications\DatabaseNotification  $notification
     * @return mixed
     */
    public function view(User $user, DatabaseNotification $notification = null)
    {
        return $user->tokenCan('notifications.show')
            && (! $notification || (
                $notification->notifiable_id == $user->id
                && $notification->notifiable_type == 'App\User'
            ));
    }

    /**
     * Determine whether the user can update the notification.
     *
     * @param  \App\User  $user
     * @param  \Illuminate\Notifications\DatabaseNotification  $notification
     * @return mixed
     */
    public function update(User $user, DatabaseNotification $notification)
    {
        return $user->tokenCan('notifications.update')
            && $notification->notifiable_id == $user->id
            && $notification->notifiable_type == 'App\User';
    }

    /**
     * Determine whether the user can delete the notification.
     *
     * @param  \App\User  $user
     * @param  \Illuminate\Notifications\DatabaseNotification  $notification
     * @return mixed
     */
    public function delete(User $user, DatabaseNotification $notification)
    {
        return $user->tokenCan('notifications.delete')
            && $notification->notifiable_id == $user->id
            && $notification->notifiable_type == 'App\User';
    }
}
