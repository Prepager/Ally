<?php

namespace ZapsterStudios\Ally\Tests\Middleware;

use Ally;
use App\Team;
use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Middleware\Subscribed;

class SubscribedTest extends TestCase
{
    /**
     * Enable env loading.
     *
     * @return void
     */
    public function __construct()
    {
        $this->usesEnv = true;

        parent::__construct();
    }

    /**
     * Setup test route.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->app->router->get('test-route', [
            'middleware' => Subscribed::class,
            function () {
                return 'Middleware passed.';
            }
        ]);
    }

    /**
     * @test
     * @group Middleware
     */
    public function guestCanNotPass()
    {
        $response = $this->json('GET', 'test-route');

        $response->assertStatus(401);
    }

    /**
     * @test
     * @group Middleware
     */
    public function userCanNotPassWithInvalidTeam()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['*']);
        $response = $this->json('GET', 'test-route');

        $response->assertStatus(403);
    }

    /**
     * @test
     * @group Middleware
     */
    public function userCanNotPassWithUnsubscribedTeam()
    {
        $user = factory(User::class)->create();
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
     * @group Subscription
     */
    public function userCanPassWithSubscribedTeam()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create(['user_id' => $user->id]));

        $user->team_id = $team->id;
        $user->save();

        Passport::actingAs($user, ['teams.billing']);
        $response = $this->json('POST', route('subscription', $team->slug), [
            'plan' => 'valid-first-plan',
            'nonce' => 'fake-valid-nonce',
        ]);

        $response->assertStatus(200);

        $response = $this->json('GET', 'test-route');

        $response->assertStatus(200);
    }
}