<?php

namespace ZapsterStudios\Ally\Events\Teams\Members;

use App\Team;
use Illuminate\Queue\SerializesModels;

class TeamMemberInvited
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Team $team, $email)
    {
        $this->team = $team;
        $this->email = $email;
    }
}
