<?php

namespace ZapsterStudios\TeamPay\Tests\Feature;

use App\Team;
use App\User;
use Laravel\Passport\Passport;
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
    public function nonMemberCanNotRetrieveMembers()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();

        Passport::actingAs($user, ['view-teams']);
        $response = $this->json('GET', route('team.members.index', $team->slug));

        $response->assertStatus(403);
    }

    /** @test */
    public function memberCanRetrieveMembers()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create());
        $extra = $team->members()->save(factory(User::class)->create());

        Passport::actingAs($extra, ['view-teams']);
        $response = $this->json('GET', route('team.members.index', $team->slug));

        $response->assertStatus(200);
        $response->assertJson([
            ['name' => $user->name],
            ['name' => $extra->name],
        ]);

        $this->assertCount(2, $response->getData());
    }

    /** @test */
    public function ownerCanUpdateMemberGroup()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create(['user_id' => $user->id]));

        $extra = $team->members()->save(factory(User::class)->create());
        $member = $team->teamMembers()->orderBy('user_id', 'desc')->firstOrFail();

        Passport::actingAs($user, ['manage-teams']);
        $response = $this->json('PUT', route('team.members.update', [$team->slug, $member->id]), [
            'group' => 'member',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('team_members', [
            'user_id' => $extra->id,
            'team_id' => $team->id,
            'group' => 'member',
        ]);
    }

    /** @test */
    public function ownerCanDeleteMember()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create(['user_id' => $user->id]));

        $extra = $team->members()->save(factory(User::class)->create());
        $member = $team->teamMembers()->orderBy('user_id', 'desc')->firstOrFail();

        Passport::actingAs($user, ['manage-teams']);
        $response = $this->json('DELETE', route('team.members.destroy', [$team->slug, $member->id]));

        $response->assertStatus(200);
    }
}
