<?php

namespace ZapsterStudios\TeamPay\Tests\Feature;

use TeamPay;
use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\TeamPay\Tests\TestCase;

class ImpersonationTest extends TestCase
{
    /** @test */
    public function nonAdminCanImpersonateValidUser()
    {
        $user = factory(User::class)->create();
        $extra = factory(User::class)->create();

        Passport::actingAs($user, ['manage-application']);
        $response = $this->json('POST', route('dashboard.users.impersonation.store', $extra->id));

        $response->assertStatus(403);
    }

    /** @test */
    public function adminCanNotImpersonateInvalidUser()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        Passport::actingAs($user, ['manage-application']);
        $response = $this->json('POST', route('dashboard.users.impersonation.store', 'not-an-id'));

        $response->assertStatus(404);
    }

    /** @test */
    public function adminCanImpersonateValidUser()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        $extra = factory(User::class)->create();

        Passport::actingAs($user, ['manage-application']);
        $response = $this->json('POST', route('dashboard.users.impersonation.store', $extra->id));

        $response->assertStatus(200);
        $response->assertJson([
            'token' => [
                'user_id' => $extra->id,
            ],
        ]);
        $response->assertJsonStructure([
            'accessToken', 'token',
        ]);
    }

    /** @test */
    public function adminCanStopImpersonatingUser()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        $extra = factory(User::class)->create();

        $response = $this->json('POST', route('login'), [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(200);

        $token = json_decode($response->getContent());
        $response = $this->json('POST', route('dashboard.users.impersonation.store', $extra->id), [], [
            'HTTP_Authorization' => 'Bearer '.$token->access_token,
        ]);

        $response->assertStatus(200);

        $token = json_decode($response->getContent());
        $response = $this->json('DELETE', route('dashboard.users.impersonation.destroy'), [], [
            'HTTP_Authorization' => 'Bearer '.$token->accessToken,
        ]);

        $response->assertStatus(200);
    }
}
