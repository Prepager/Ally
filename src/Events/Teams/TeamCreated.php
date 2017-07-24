<?php

namespace ZapsterStudios\Ally\Events\Teams;

use App\Team;
use Illuminate\Queue\SerializesModels;

class TeamCreated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param  \App\Team  $team
     * @return void
     */
    public function __construct(Team $team)
    {
        $this->team = $team;
    }
}
