<?php

namespace ZapsterStudios\Ally\Events\Teams\Members;

use App\Team;
use App\User;
use Illuminate\Queue\SerializesModels;

class TeamMemberKicked
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  \App\Team  $team
     * @param  \App\User  $user
     * @return void
     */
    public function __construct(Team $team, User $user)
    {
        $this->team = $team;
        $this->user = $user;
    }
}
