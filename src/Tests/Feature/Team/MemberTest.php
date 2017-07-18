<?php

namespace ZapsterStudios\TeamPay\Tests\Feature\Team;

use App\Team;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Event;
use ZapsterStudios\TeamPay\Tests\TestCase;
use ZapsterStudios\TeamPay\Events\Teams\Members\TeamMemberKicked;

class MemberTest extends TestCase
{
    /**
     * @test
     * @group Team
     */
    public function guestCanNotRetrieveMembers()
    {
        $team = factory(Team::class)->create();
        $response = $this->json('GET', route('teams.members.index', $team->slug));

        $response->assertStatus(401);
    }

    /**
     * @test
     * @group Team
     */
    public function nonMemberCanNotRetrieveMembers()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();

        Passport::actingAs($user, ['view-teams']);
        $response = $this->json('GET', route('teams.members.index', $team->slug));

        $response->assertStatus(403);
    }

    /**
     * @test
     * @group Team
     */
    public function memberCanRetrieveMembers()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create());
        $extra = $team->members()->save(factory(User::class)->create());

        Passport::actingAs($extra, ['view-teams']);
        $response = $this->json('GET', route('teams.members.index', $team->slug));

        $response->assertStatus(200);
        $response->assertJson([
            ['name' => $user->name],
            ['name' => $extra->name],
        ]);

        $this->assertCount(2, $response->getData());
    }

    /**
     * @test
     * @group Team
     */
    public function ownerCanUpdateMemberGroup()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create(['user_id' => $user->id]));

        $extra = $team->members()->save(factory(User::class)->create());
        $member = $team->teamMembers()->orderBy('user_id', 'desc')->firstOrFail();

        Passport::actingAs($user, ['manage-teams']);
        $response = $this->json('PUT', route('teams.members.update', [$team->slug, $member->id]), [
            'group' => 'member',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('team_members', [
            'user_id' => $extra->id,
            'team_id' => $team->id,
            'group' => 'member',
        ]);
    }

    /**
     * @test
     * @group Team
     */
    public function ownerCanNotUpdateMemberWithInvalidGroup()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create(['user_id' => $user->id]));

        $extra = $team->members()->save(factory(User::class)->create());
        $member = $team->teamMembers()->orderBy('user_id', 'desc')->firstOrFail();

        Passport::actingAs($user, ['manage-teams']);
        $response = $this->json('PUT', route('teams.members.update', [$team->slug, $member->id]), [
            'group' => 'invalid-group',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     * @group Team
     */
    public function ownerCanDeleteMember()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create(['user_id' => $user->id]));

        $extra = $team->members()->save(factory(User::class)->create());
        $member = $team->teamMembers()->orderBy('user_id', 'desc')->firstOrFail();

        Passport::actingAs($user, ['manage-teams']);
        $response = $this->json('DELETE', route('teams.members.destroy', [$team->slug, $member->id]));

        $response->assertStatus(200);

        Event::assertDispatched(TeamMemberKicked::class, function ($e) use ($team, $extra) {
            return $e->team->slug == $team->slug
                && $e->user->id == $extra->id;
        });
    }
}
