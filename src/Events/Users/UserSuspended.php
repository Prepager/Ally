<?php

namespace ZapsterStudios\Ally\Events\Users;

use App\User;
use Illuminate\Queue\SerializesModels;

class UserSuspended
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}
