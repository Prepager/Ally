<?php

namespace ZapsterStudios\TeamPay\Tests\Feature;

use App\User;
use ZapsterStudios\TeamPay\Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /** @test */
    public function userCanLoginWithValidCredentials()
    {
        $user = factory(User::class)->create();

        $response = $this->json('POST', '/login', [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token_type', 'expires_in',
            'access_token', 'refresh_token',
        ]);
    }

    /** @test */
    public function userCanNotLoginWithInvalidCredentials()
    {
        $response = $this->json('POST', '/login', [
            'email' => 'unregistered@example.com',
            'password' => 'unregistered',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function userCanRefreshTokenWithValidToken()
    {
        //
    }

    /** @test */
    public function userCanNotRefreshTokenWithInvalidToken()
    {
        //
    }

    /** @test */
    public function userCanLogout()
    {
        //
    }
}
