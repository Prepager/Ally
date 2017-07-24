<?php

namespace ZapsterStudios\Ally\Events\Teams;

use App\Team;
use Illuminate\Queue\SerializesModels;

class TeamRestored
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Team $team)
    {
        $this->team = $team;
    }
}
