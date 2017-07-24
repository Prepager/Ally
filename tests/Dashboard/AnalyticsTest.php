<?php

namespace ZapsterStudios\Ally\Tests\Dashboard;

use Ally;
use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;

class AnalyticsTest extends TestCase
{
    /**
     * @test
     * @group Dashboard
     */
    public function guestCanNotAccessDashboard()
    {
        $response = $this->json('GET', route('dashboard.index'));

        $response->assertStatus(401);
    }

    /**
     * @test
     * @group Dashboard
     */
    public function nonAdminCanNotAccessDashboard()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('GET', route('dashboard.index'));

        $response->assertStatus(403);
    }

    /**
     * @test
     * @group Dashboard
     */
    public function adminCanAccessDashboard()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('GET', route('dashboard.index'));

        $response->assertStatus(200);
        // Assert response.
    }
}
