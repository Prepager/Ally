<?php

namespace ZapsterStudios\Ally\Events\Users;

use App\User;
use Illuminate\Queue\SerializesModels;

class UserUpdated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
