<?php

namespace ZapsterStudios\TeamPay\Tests\Feature;

use App\Team;
use ZapsterStudios\TeamPay\Tests\TestCase;

class TeamMemberTest extends TestCase
{
    /** @test */
    public function guestCanNotRetrieveMembers()
    {
        $team = factory(Team::class)->create();
        $response = $this->json('GET', route('team.members.index', $team->slug));

        $response->assertStatus(401);
    }

    /** @test */
    public function memberCanRetrieveMembers()
    {
        //
    }
}
