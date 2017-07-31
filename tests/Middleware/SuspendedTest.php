<?php

namespace ZapsterStudios\Ally\Tests\Middleware;

use App\Team;
use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Middleware\Suspended;

class SuspendedTest extends TestCase
{
    /**
     * Setup test route.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->app->router->get('test-route', [
            'middleware' => Suspended::class,
            function () {
                return 'Middleware passed.';
            },
        ]);
    }

    /**
     * @test
     * @group Middleware
     */
    public function guestCanPass()
    {
        $response = $this->json('GET', 'test-route');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @group Middleware
     */
    public function nonSuspendedUserCanPassWithUnsuspendedTeam()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create(['user_id' => $user->id]));

        $user->team_id = $team->id;
        $user->save();

        Passport::actingAs($user, ['*']);
        $response = $this->json('GET', 'test-route');

        $response->assertStatus(200);
    }

    /**
     * @test
     * @group Middleware
     */
    public function suspendedUserCanNotPass()
    {
        $user = factory(User::class)->states('suspended')->create();
        $team = $user->teams()->save(factory(Team::class)->create(['user_id' => $user->id]));

        $user->team_id = $team->id;
        $user->save();

        Passport::actingAs($user, ['*']);
        $response = $this->json('GET', 'test-route');

        $response->assertStatus(403);
    }

    /**
     * @test
     * @group Middleware
     */
    public function suspendedTeamCanNotPass()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->states('suspended')->create(['user_id' => $user->id]));

        $user->team_id = $team->id;
        $user->save();

        Passport::actingAs($user, ['*']);
        $response = $this->json('GET', 'test-route');

        $response->assertStatus(403);
    }
}
