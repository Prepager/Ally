<?php

namespace ZapsterStudios\TeamPay\Events\Teams;


use App\Team;
use Illuminate\Queue\SerializesModels;

class TeamDeleated
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
