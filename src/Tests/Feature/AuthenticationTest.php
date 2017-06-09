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

        return json_decode($response->getContent());
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

    /**
     * @test
     * @depends userCanLoginWithValidCredentials
     */
    public function userCanLogout($token)
    {
        $response = $this->json('POST', '/logout', [], [
            'HTTP_Authorization' => 'Bearer '.$token->access_token,
        ]);

        $response->assertStatus(200);

        return $token;
    }

    /**
     * @test
     * @depends userCanLogout
     */
    public function userCanRefreshTokenWithValidToken($token)
    {
        $response = $this->json('POST', '/login/refresh', [
            'token' => $token->refresh_token,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token_type', 'expires_in',
            'access_token', 'refresh_token',
        ]);
    }

    /** @test */
    public function userCanNotRefreshTokenWithInvalidToken()
    {
        $response = $this->json('POST', '/login/refresh', [
            'token' => 'invalid-token',
        ]);

        $response->assertStatus(400);
    }
}
