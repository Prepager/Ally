<?php

namespace ZapsterStudios\TeamPay\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view itself.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->tokenCan('user.show');
    }

    /**
     * Determine whether the user can update itself.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->tokenCan('user.update');
    }

    /**
     * Determine whether the user can change password.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function password(User $user)
    {
        return $user->tokenCan('user.password');
    }

    /**
     * Determine whether the user can access the dashboard.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function admin(User $user)
    {
        return $user->tokenCan('user.admin');
    }
}
