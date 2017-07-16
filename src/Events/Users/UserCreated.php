<?php

namespace ZapsterStudios\TeamPay\Events\Users;

use App\User;
use Illuminate\Queue\SerializesModels;

class UserCreated
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