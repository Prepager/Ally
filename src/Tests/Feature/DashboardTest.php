<?php

namespace ZapsterStudios\TeamPay\Tests\Feature;

use TeamPay;
use App\Team;
use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\TeamPay\Tests\TestCase;

class DashboardTest extends TestCase
{
    /** @test */
    public function guestCanNotAccessDashboard()
    {
        $response = $this->json('GET', route('dashboard'));

        $response->assertStatus(401);
    }

    /** @test */
    public function nonAdminCanNotAccessDashboard()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['access-dashboard']);
        $response = $this->json('GET', route('dashboard'));

        $response->assertStatus(403);
    }

    /** @test */
    public function adminCanAccessDashboard()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        Passport::actingAs($user, ['access-dashboard']);
        $response = $this->json('GET', route('dashboard'));

        $response->assertStatus(200);
        // Assert response.
    }

    /** @test */
    public function adminCanRetrieveUsers()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        factory(User::class, 10)->create();

        Passport::actingAs($user, ['access-dashboard']);
        $response = $this->json('GET', route('dashboard.users'));

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

        Passport::actingAs($user, ['access-dashboard']);
        $response = $this->json('GET', route('dashboard.users.show', $extra->id));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $extra->id,
            'teams' => [
                [
                    'slug' => $team->slug,
                ], 
            ]
        ]);
    }

    /** @test */
    public function adminCanSearchForUser()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        $extra = factory(User::class)->create();

        Passport::actingAs($user, ['access-dashboard']);
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
    public function adminCanRetrieveTeams()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        factory(Team::class, 10)->create();

        Passport::actingAs($user, ['access-dashboard']);
        $response = $this->json('GET', route('dashboard.teams'));

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

        Passport::actingAs($user, ['access-dashboard']);
        $response = $this->json('GET', route('dashboard.teams.show', $team->id));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $team->id,
            'members' => [
                [
                    'id' => $extra->id,
                ], 
            ]
        ]);
    }

    /** @test */
    public function adminCanSearchForTeam()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        $team = factory(Team::class)->create();

        Passport::actingAs($user, ['access-dashboard']);
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
}
