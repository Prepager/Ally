<?php

namespace ZapsterStudios\Ally\Tests\Feature;

use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;

class AppDataTest extends TestCase
{
    /** @test */
    public function guestCanRetrieveAppConfig()
    {
        $response = $this->json('GET', route('app'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'plans', 'groups', // Reset of config options.
        ]);
    }

    /** @test */
    public function guestCanNotRetrieveSubscriptionToken()
    {
        $response = $this->json('GET', route('app.token'));

        $response->assertStatus(401);
    }

    /** @test */
    public function userCanRetrieveSubscriptionToken()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user);
        $response = $this->json('GET', route('app.token'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
        ]);
    }
}
