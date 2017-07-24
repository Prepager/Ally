<?php

namespace ZapsterStudios\Ally\Tests\Feature;

use Ally;
use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;

class ImpersonationTest extends TestCase
{
    /**
     * @test
     * @group Dashboard
     */
    public function nonAdminCanImpersonateValidUser()
    {
        $user = factory(User::class)->create();
        $extra = factory(User::class)->create();

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('POST', route('dashboard.users.impersonation.store', $extra->id));

        $response->assertStatus(403);
    }

    /**
     * @test
     * @group Dashboard
     */
    public function adminCanNotImpersonateInvalidUser()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('POST', route('dashboard.users.impersonation.store', 'not-an-id'));

        $response->assertStatus(404);
    }

    /**
     * @test
     * @group Dashboard
     */
    public function adminCanImpersonateValidUser()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        $extra = factory(User::class)->create();

        Passport::actingAs($user, ['user.admin']);
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

    /**
     * @test
     * @group Dashboard
     */
    public function adminCanStopImpersonatingUser()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

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
