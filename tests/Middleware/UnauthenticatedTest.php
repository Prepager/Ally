<?php

namespace ZapsterStudios\Ally\Tests\Middleware;

use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Middleware\Unauthenticated;

class UnauthenticatedTest extends TestCase
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
            'middleware' => Unauthenticated::class,
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
    public function userCanNotPass()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['*']);
        $response = $this->json('GET', 'test-route');

        $response->assertStatus(403);
    }
}
