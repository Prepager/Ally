<?php

namespace ZapsterStudios\Ally\Tests\Feature\Subscription;

use Ally;
use App\Team;
use App\User;
use Braintree_Configuration;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Event;
use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Models\TeamMember;
use ZapsterStudios\Ally\Events\Subscriptions\SubscriptionCreated;
use ZapsterStudios\Ally\Events\Subscriptions\SubscriptionResumed;
use ZapsterStudios\Ally\Events\Subscriptions\SubscriptionSwapped;
use ZapsterStudios\Ally\Events\Subscriptions\SubscriptionCancelled;

class SubscriptionTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        Braintree_Configuration::environment('sandbox');
        Ally::addPlan('valid-first-plan', 'Valid First Plan', 5);
        Ally::addPlan('valid-second-plan', 'Valid Second Plan', 10);
    }

    /**
     * @test
     * @group Subscription
     */
    public function guestCanNotSubscribe()
    {
        $team = factory(Team::class)->create();

        $response = $this->json('POST', route('subscription', $team->slug));

        $response->assertStatus(401);
    }

    /** @test @group subscription */
    public function memberCanNotSubscribe()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        $member = $team->members()->save(factory(TeamMember::class)->create([
            'user_id' => $user->id,
            'team_id' => $team->id,
            'group' => 'member',
        ]));

        $response = $this->json('POST', route('subscription', $team->slug));

        $response->assertStatus(401);
    }

    /**
     * @test
     * @group Subscription
     */
    public function ownerCanNotSubscribeWithoutData()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create(['user_id' => $user->id]));

        Passport::actingAs($user, ['teams.billing']);
        $response = $this->json('POST', route('subscription', $team->slug));

        $response->assertStatus(422);
    }

    /**
     * @test
     * @group Subscription
     */
    public function ownerCanNotSubscribeWithInvalidPlan()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create(['user_id' => $user->id]));

        Passport::actingAs($user, ['teams.billing']);
        $response = $this->json('POST', route('subscription', $team->slug), [
            'plan' => 'invalid-plan-id',
            'nonce' => 'fake-valid-nonce',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     * @group Subscription
     */
    public function ownerCanSubscribeCancelResumeAndSwapWithValidPlan()
    {
        Event::fake();

        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create(['user_id' => $user->id]));

        Passport::actingAs($user, ['teams.billing']);
        $response = $this->json('POST', route('subscription', $team->slug), [
            'plan' => 'valid-first-plan',
            'nonce' => 'fake-valid-nonce',
        ]);

        $response->assertStatus(200);
        $this->assertTrue($team->subscribed('default', 'valid-first-plan'));

        Event::assertDispatched(SubscriptionCreated::class, function ($e) use ($team) {
            return $e->team->slug == $team->slug;
        });

        $response = $this->json('POST', route('subscription.cancel', $team->slug));
        $team = $team->fresh();

        $response->assertStatus(200);
        $this->assertTrue($team->subscription()->cancelled());

        Event::assertDispatched(SubscriptionCancelled::class, function ($e) use ($team) {
            return $e->team->slug == $team->slug;
        });

        $response = $this->json('POST', route('subscription.resume', $team->slug));
        $team = $team->fresh();

        $response->assertStatus(200);
        $this->assertFalse($team->subscription()->cancelled());

        Event::assertDispatched(SubscriptionResumed::class, function ($e) use ($team) {
            return $e->team->slug == $team->slug;
        });

        $response = $this->json('POST', route('subscription', $team->slug), [
            'plan' => 'valid-second-plan',
            'nonce' => 'fake-valid-nonce',
        ]);
        $team = $team->fresh();

        $response->assertStatus(200);
        $this->assertTrue($team->subscribed('default', 'valid-second-plan'));

        Event::assertDispatched(SubscriptionSwapped::class, function ($e) use ($team) {
            return $e->team->slug == $team->slug
                && $e->subscription->braintree_plan == 'valid-second-plan';
        });
    }
}
