<?php

namespace ZapsterStudios\Ally\Tests\Feature;

use Ally;
use App\Team;
use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Event;
use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Events\Users\UserSuspended;

class UserTest extends TestCase
{
    /**
     * @test
     * @group Dashboard
     */
    public function adminCanRetrieveUsers()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        factory(User::class, 10)->create();

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('GET', route('dashboard.users.index'));

        $response->assertStatus(200);
        $this->assertEquals(User::count(), $response->getData()->total);
    }

    /**
     * @test
     * @group Dashboard
     */
    public function adminCanRetrieveUser()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        $extra = factory(User::class)->create();
        $team = $extra->teams()->save(factory(Team::class)->create());

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('GET', route('dashboard.users.show', $extra->id));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $extra->id,
            'teams' => [
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
    public function adminCanSearchForUser()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        $extra = factory(User::class)->create();

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('POST', route('dashboard.users.search'), [
            'search' => $extra->email,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
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
    public function adminCanSuspendAndUnsuspendUser()
    {
        Event::fake();

        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        $extra = factory(User::class)->create();
        $suspendedTo = Carbon::now()->addDays(5)->toDateTimeString();

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('POST', route('dashboard.users.suspension.store', $extra->id), [
            'suspended_to' => $suspendedTo,
            'suspended_reason' => 'Some test',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'suspended_to' => $suspendedTo,
            'suspended_reason' => 'Some test',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $extra->id,
            'suspended_to' => $suspendedTo,
            'suspended_reason' => 'Some test',
        ]);

        Event::assertDispatched(UserSuspended::class, function ($e) {
            return $e->user->suspended_reason == 'Some test';
        });

        $response = $this->json('DELETE', route('dashboard.users.suspension.destroy', $extra->id));

        $response->assertStatus(200);
        $response->assertJson([
            'suspended_at' => null,
            'suspended_to' => null,
            'suspended_reason' => null,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $extra->id,
            'suspended_at' => null,
            'suspended_to' => null,
            'suspended_reason' => null,
        ]);
    }
}
