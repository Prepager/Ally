<?php

namespace ZapsterStudios\Ally\Tests\Feature\Team;

use App\Team;
use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Event;
use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Models\TeamMember;
use ZapsterStudios\Ally\Events\Teams\TeamCreated;
use ZapsterStudios\Ally\Events\Teams\TeamDeleated;
use ZapsterStudios\Ally\Events\Teams\TeamRestored;

class TeamTest extends TestCase
{
    public $teamStructure = [
        'id', 'name', 'slug',
        'created_at', 'updated_at',
    ];

    /**
     * @test
     * @group Team
     */
    public function guestCanNotRetrieveTeams()
    {
        $response = $this->json('GET', route('teams.index'));

        $response->assertStatus(401);
    }

    /**
     * @test
     * @group Team
     */
    public function userCanRetrieveTeams()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['teams.show']);
        $response = $this->json('GET', route('teams.index'));

        $response->assertStatus(200);
    }

    /**
     * @test
     * @group Team
     */
    public function userCanViewOwnTeam()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create());

        Passport::actingAs($user, ['teams.show']);
        $response = $this->json('GET', route('teams.show', $team->slug));

        $response->assertStatus(200);
        $response->assertJson($team->toArray());
        $response->assertJsonStructure($this->teamStructure);
    }

    /**
     * @test
     * @group Team
     */
    public function memberCanViewTeam()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        $member = $team->members()->save(factory(TeamMember::class)->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]));

        Passport::actingAs($user, ['teams.show']);
        $response = $this->json('GET', route('teams.show', $team->slug));

        $response->assertStatus(200);
        $response->assertJson($team->toArray());
        $response->assertJsonStructure($this->teamStructure);
    }

    /**
     * @test
     * @group Team
     */
    public function userCanCreateNewTeam()
    {
        Event::fake();

        $user = factory(User::class)->create();

        Passport::actingAs($user, ['teams.create']);
        $response = $this->json('POST', route('teams.store'), [
            'name' => 'Example',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'Example',
        ]);
        $response->assertJsonStructure($this->teamStructure);

        $this->assertDatabaseHas('teams', [
            'name' => 'Example',
        ]);

        Event::assertDispatched(TeamCreated::class, function ($e) {
            return $e->team->name == 'Example';
        });
    }

    /**
     * @test
     * @group Team
     */
    public function userCanUpdateExistingTeam()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['user_id' => $user->id]);

        Passport::actingAs($user, ['teams.update']);
        $response = $this->json('PUT', route('teams.update', $team->slug), [
            'name' => 'Foobar',
        ]);

        $response->assertStatus(200);
        $response->assertJson(array_merge($team->toArray(), [
            'name' => 'Foobar',
            'slug' => str_slug('Foobar'),
        ]));
        $response->assertJsonStructure($this->teamStructure);

        $this->assertDatabaseHas('teams', [
            'name' => 'Foobar',
            'slug' => str_slug('Foobar'),
        ]);
    }

    /**
     * @test
     * @group Team
     */
    public function userCanDeleteTeam()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['user_id' => $user->id]);

        $user->team_id = $team->id;

        Passport::actingAs($user, ['teams.delete']);
        $response = $this->json('DELETE', route('teams.destroy', $team->slug));

        $response->assertStatus(200);
        $this->assertSoftDeleted('teams', [
            'slug' => $team->slug,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'team_id' => 0,
        ]);

        Event::assertDispatched(TeamDeleated::class, function ($e) use ($team) {
            return $e->team->slug == $team->slug;
        });
    }

    /**
     * @test
     * @group Team
     */
    public function userCanRestoreTeam()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['user_id' => $user->id]);

        $team->delete();

        Passport::actingAs($user, ['teams.restore']);
        $response = $this->json('POST', route('teams.restore', $team->slug));

        $response->assertStatus(200);
        $this->assertDatabaseHas('teams', [
            'slug' => $team->slug,
            'deleted_at' => null,
        ]);

        Event::assertDispatched(TeamRestored::class, function ($e) use ($team) {
            return $e->team->slug == $team->slug;
        });
    }

    /**
     * @test
     * @group Team
     */
    public function canGenerateUniqueSlug()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['teams.create']);
        $team1 = $this->json('POST', route('teams.store'), ['name' => 'Example Community']);
        $team2 = $this->json('POST', route('teams.store'), ['name' => 'Example-Community']);
        $team3 = $this->json('POST', route('teams.store'), ['name' => 'Example_Community']);

        $team1->assertJson(['slug' => 'example-community']);
        $team2->assertJson(['slug' => 'example-community-1']);
        $team3->assertJson(['slug' => 'example-community-2']);
    }

    /**
     * @test
     * @group Team
     */
    public function suspendedTeamCanNotRetrieveData()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'suspended_at' => Carbon::now()->subDays(1),
            'suspended_to' => Carbon::now()->addDays(1),
            'suspended_reason' => 'Some test',
        ]));

        Passport::actingAs($user, ['teams.show']);
        $response = $this->json('GET', route('teams.show', $team->slug));

        $response->assertStatus(403);
        $response->assertJson([
            'suspended_at' => $team->suspended_at,
            'suspended_to' => $team->suspended_to,
            'suspended_reason' => $team->suspended_reason,
        ]);
    }

    /**
     * @test
     * @group Team
     */
    public function expiredSuspendedUserCanRetrieveData()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create([
            'suspended_at' => Carbon::now()->subDays(1),
            'suspended_to' => Carbon::now()->subMinutes(5),
            'suspended_reason' => 'Some test',
        ]));

        Passport::actingAs($user, ['teams.show']);
        $response = $this->json('GET', route('teams.show', $team->slug));

        $response->assertStatus(200);
    }
}
