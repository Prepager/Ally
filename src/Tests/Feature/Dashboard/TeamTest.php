<?php

namespace ZapsterStudios\Ally\Tests\Feature;

use Ally;
use App\Team;
use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Event;
use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Events\Teams\TeamSuspended;

class TeamTest extends TestCase
{
    /**
     * @test
     * @group Dashboard
     */
    public function adminCanRetrieveTeams()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        factory(Team::class, 10)->create();

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('GET', route('dashboard.teams.index'));

        $response->assertStatus(200);
        $this->assertEquals(10, $response->getData()->total);
    }

    /**
     * @test
     * @group Dashboard
     */
    public function adminCanRetrieveTeam()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        $extra = factory(User::class)->create();
        $team = $extra->teams()->save(factory(Team::class)->create());

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('GET', route('dashboard.teams.show', $team->id));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $team->id,
            'members' => [
                [
                    'id' => $extra->id,
                ],
            ],
        ]);
    }

    /**
     * @test
     * @group Dashboard
     */
    public function adminCanSearchForTeam()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        $team = factory(Team::class)->create();

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('POST', route('dashboard.teams.search'), [
            'search' => $team->slug,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                [
                    'slug' => $team->slug,
                ],
            ],
        ]);
    }

    /**
     * @test
     * @group Dashboard
     */
    public function adminCanSuspendAndUnsuspendTeam()
    {
        Event::fake();

        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        $extra = factory(Team::class)->create();
        $suspendedTo = Carbon::now()->addDays(5)->toDateTimeString();

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('POST', route('dashboard.teams.suspension.store', $extra->slug), [
            'suspended_to' => $suspendedTo,
            'suspended_reason' => 'Some test',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'suspended_to' => $suspendedTo,
            'suspended_reason' => 'Some test',
        ]);

        $this->assertDatabaseHas('teams', [
            'slug' => $extra->slug,
            'suspended_to' => $suspendedTo,
            'suspended_reason' => 'Some test',
        ]);

        Event::assertDispatched(TeamSuspended::class, function ($e) {
            return $e->team->suspended_reason == 'Some test';
        });

        $response = $this->json('DELETE', route('dashboard.teams.suspension.destroy', $extra->slug));

        $response->assertStatus(200);
        $response->assertJson([
            'suspended_at' => null,
            'suspended_to' => null,
            'suspended_reason' => null,
        ]);

        $this->assertDatabaseHas('teams', [
            'slug' => $extra->slug,
            'suspended_at' => null,
            'suspended_to' => null,
            'suspended_reason' => null,
        ]);
    }
}
