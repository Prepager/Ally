<?php

namespace ZapsterStudios\TeamPay\Tests\Feature;

use App\Team;
use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\TeamPay\Tests\TestCase;
use ZapsterStudios\TeamPay\Models\TeamMember;

class TeamTest extends TestCase
{
    public $teamStructure = [
        'id', 'name', 'slug',
        'created_at', 'updated_at',
    ];

    /** @test */
    public function guestCanNotRetrieveTeams()
    {
        $response = $this->json('GET', route('teams.index'));

        $response->assertStatus(401);
    }

    /** @test */
    public function userCanRetrieveTeams()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['view-teams']);
        $response = $this->json('GET', route('teams.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function userCanViewOwnTeam()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create());

        Passport::actingAs($user, ['view-teams']);
        $response = $this->json('GET', route('teams.show', $team->slug));

        $response->assertStatus(200);
        $response->assertJson($team->toArray());
        $response->assertJsonStructure($this->teamStructure);
    }

    /** @test */
    public function memberCanViewTeam()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        $member = $team->members()->save(factory(TeamMember::class)->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
        ]));

        Passport::actingAs($user, ['view-teams']);
        $response = $this->json('GET', route('teams.show', $team->slug));

        $response->assertStatus(200);
        $response->assertJson($team->toArray());
        $response->assertJsonStructure($this->teamStructure);
    }

    /** @test */
    public function userCanCreateNewTeam()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['manage-teams']);
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
    }

    /** @test */
    public function userCanUpdateExistingTeam()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['user_id' => $user->id]);

        Passport::actingAs($user, ['manage-teams']);
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

    /** @test */
    public function userCanDeleteTeam()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['user_id' => $user->id]);

        Passport::actingAs($user, ['manage-teams']);
        $response = $this->json('DELETE', route('teams.destroy', $team->slug));

        $response->assertStatus(200);
        $this->assertSoftDeleted('teams', [
            'slug' => $team->slug,
        ]);
    }

    /** @test */
    public function userCanRestoreTeam()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create(['user_id' => $user->id]);

        $team->delete();

        Passport::actingAs($user, ['manage-teams']);
        $response = $this->json('POST', route('teams.restore', $team->slug));

        $response->assertStatus(200);
        $this->assertDatabaseHas('teams', [
            'slug' => $team->slug,
            'deleted_at' => null,
        ]);
    }

    /** @test */
    public function canGenerateUniqueSlug()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['manage-teams']);
        $team1 = $this->json('POST', route('teams.store'), ['name' => 'Example Community']);
        $team2 = $this->json('POST', route('teams.store'), ['name' => 'Example-Community']);
        $team3 = $this->json('POST', route('teams.store'), ['name' => 'Example_Community']);

        $team1->assertJson(['slug' => 'example-community']);
        $team2->assertJson(['slug' => 'example-community-1']);
        $team3->assertJson(['slug' => 'example-community-2']);
    }
}
