<?php

namespace ZapsterStudios\TeamPay\Tests\Feature;

use TeamPay;
use App\Team;
use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use ZapsterStudios\TeamPay\Tests\TestCase;

class DashboardTest extends TestCase
{
    /** @test */
    public function guestCanNotAccessDashboard()
    {
        $response = $this->json('GET', route('dashboard.index'));

        $response->assertStatus(401);
    }

    /** @test */
    public function nonAdminCanNotAccessDashboard()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['manage-application']);
        $response = $this->json('GET', route('dashboard.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function adminCanAccessDashboard()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        Passport::actingAs($user, ['manage-application']);
        $response = $this->json('GET', route('dashboard.index'));

        $response->assertStatus(200);
        // Assert response.
    }

    /** @test */
    public function adminCanRetrieveUsers()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        factory(User::class, 10)->create();

        Passport::actingAs($user, ['manage-application']);
        $response = $this->json('GET', route('dashboard.users.index'));

        $response->assertStatus(200);
        $this->assertEquals(11, $response->getData()->total);
    }

    /** @test */
    public function adminCanRetrieveUser()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        $extra = factory(User::class)->create();
        $team = $extra->teams()->save(factory(Team::class)->create());

        Passport::actingAs($user, ['manage-application']);
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

    /** @test */
    public function adminCanSearchForUser()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        $extra = factory(User::class)->create();

        Passport::actingAs($user, ['manage-application']);
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

    /** @test */
    public function adminCanSuspendAndUnsuspendUser()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        $extra = factory(User::class)->create();
        $suspendedTo = Carbon::now()->addDays(5)->toDateTimeString();

        Passport::actingAs($user, ['manage-application']);
        $response = $this->json('POST', route('dashboard.users.suspend', $extra->id), [
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

        $response = $this->json('POST', route('dashboard.users.unsuspend', $extra->id));

        $response->assertStatus(200);
        $response->assertJson([
            'suspended_at' => NULL,
            'suspended_to' => NULL,
            'suspended_reason' => NULL,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $extra->id,
            'suspended_at' => NULL,
            'suspended_to' => NULL,
            'suspended_reason' => NULL,
        ]);
    }

    /** @test */
    public function adminCanRetrieveTeams()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        factory(Team::class, 10)->create();

        Passport::actingAs($user, ['manage-application']);
        $response = $this->json('GET', route('dashboard.teams.index'));

        $response->assertStatus(200);
        $this->assertEquals(10, $response->getData()->total);
    }

    /** @test */
    public function adminCanRetrieveTeam()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        $extra = factory(User::class)->create();
        $team = $extra->teams()->save(factory(Team::class)->create());

        Passport::actingAs($user, ['manage-application']);
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

    /** @test */
    public function adminCanSearchForTeam()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        $team = factory(Team::class)->create();

        Passport::actingAs($user, ['manage-application']);
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

    /** @test */
    public function adminCanSuspendAndUnsuspendTeam()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        $extra = factory(Team::class)->create();
        $suspendedTo = Carbon::now()->addDays(5)->toDateTimeString();

        Passport::actingAs($user, ['manage-application']);
        $response = $this->json('POST', route('dashboard.teams.suspend', $extra->slug), [
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

        $response = $this->json('POST', route('dashboard.teams.unsuspend', $extra->slug));

        $response->assertStatus(200);
        $response->assertJson([
            'suspended_at' => NULL,
            'suspended_to' => NULL,
            'suspended_reason' => NULL,
        ]);

        $this->assertDatabaseHas('teams', [
            'slug' => $extra->slug,
            'suspended_at' => NULL,
            'suspended_to' => NULL,
            'suspended_reason' => NULL,
        ]);
    }
}
