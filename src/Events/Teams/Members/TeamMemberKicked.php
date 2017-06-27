<?php

namespace ZapsterStudios\TeamPay\Events\Teams\Members;

use App\Team;
use App\User;
use Illuminate\Queue\SerializesModels;

class TeamMemberKicked
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Team $team, User $user)
    {
        $this->team = $team;
        $this->user = $user;
    }
}
