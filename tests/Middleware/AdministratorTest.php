<?php

namespace ZapsterStudios\Ally\Tests\Middleware;

use Ally;
use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Middleware\Administrator;

class AdministratorTest extends TestCase
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
            'middleware' => Administrator::class,
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
    public function nonAdminCanNotPass()
    {
        $user = factory(User::class)->states('verified')->create();

        Passport::actingAs($user, ['*']);
        $response = $this->json('GET', 'test-route');

        $response->assertStatus(403);
    }

    /**
     * @test
     * @group Middleware
     */
    public function adminCanNotPassWithoutScope()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        Passport::actingAs($user, ['']);
        $response = $this->json('GET', 'test-route');

        $response->assertStatus(403);
    }

    /**
     * @test
     * @group Middleware
     */
    public function adminCanNotPassWithoutBeingVerified()
    {
        $user = factory(User::class)->create();
        Ally::setAdmins([$user->email]);

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('GET', 'test-route');

        $response->assertStatus(403);
    }

    /**
     * @test
     * @group Middleware
     */
    public function adminCanPass()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('GET', 'test-route');

        $response->assertStatus(200);
    }
}