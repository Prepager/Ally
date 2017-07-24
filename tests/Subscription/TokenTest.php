<?php

namespace ZapsterStudios\Ally\Tests\Subscription;

use App\Team;
use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;

class TokenTest extends TestCase
{
    /**
     * @test
     * @group Subscription
     */
    public function guestCanNotRetrieveSubscriptionToken()
    {
        $team = factory(Team::class)->create();

        $response = $this->json('POST', route('subscription.token', $team));

        $response->assertStatus(401);
    }

    /**
     * @test
     * @group Subscription
     */
    public function memberCanNotRetrieveSubscriptionToken()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create());

        Passport::actingAs($user, ['teams.billing']);
        $response = $this->json('POST', route('subscription.token', $team));

        $response->assertStatus(403);
    }

    /**
     * @test
     * @group Subscription
     */
    public function ownerCanRetrieveSubscriptionToken()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create(['user_id' => $user->id]));

        Passport::actingAs($user, ['teams.billing']);
        $response = $this->json('POST', route('subscription.token', $team));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token',
        ]);
    }
}
