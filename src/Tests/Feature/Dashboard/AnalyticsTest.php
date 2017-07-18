<?php

namespace ZapsterStudios\TeamPay\Tests\Feature;

use TeamPay;
use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\TeamPay\Tests\TestCase;

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

        Passport::actingAs($user, ['manage-application']);
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
        TeamPay::setAdmins([$user->email]);

        Passport::actingAs($user, ['manage-application']);
        $response = $this->json('GET', route('dashboard.index'));

        $response->assertStatus(200);
        // Assert response.
    }
}
