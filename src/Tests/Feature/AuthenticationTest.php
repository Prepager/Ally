<?php

namespace ZapsterStudios\TeamPay\Tests\Feature;

use App\User;
use App\Team;
use Laravel\Passport\Passport;
use ZapsterStudios\TeamPay\Tests\TestCase;

class AuthenticationTest extends TestCase
{
    /** @test */
    public function userCanLoginWithValidCredentials()
    {
        $user = factory(User::class)->create();

        $response = $this->json('POST', route('login'), [
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
        $response = $this->json('POST', route('login'), [
            'email' => 'unregistered@example.com',
            'password' => 'unregistered',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function userCanRefreshTokenWithValidToken()
    {
        $user = factory(User::class)->create();

        $response = $this->json('POST', route('login'), [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(200);

        $token = json_decode($response->getContent());
        $response = $this->json('POST', route('refresh'), [
            'refresh_token' => $token->refresh_token,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'token_type', 'expires_in',
            'access_token', 'refresh_token',
        ]);
    }

    /** @test */
    public function userCanLogout()
    {
        $user = factory(User::class)->create();

        $response = $this->json('POST', route('login'), [
            'email' => $user->email,
            'password' => 'secret',
        ]);

        $response->assertStatus(200);

        $token = json_decode($response->getContent());
        $response = $this->json('POST', route('logout'), [], [
            'HTTP_Authorization' => 'Bearer '.$token->access_token,
        ]);

        $response->assertStatus(200);
    }

    /** @test */
    public function userCanNotRefreshTokenWithInvalidToken()
    {
        $response = $this->json('POST', route('refresh'), [
            'refresh_token' => 'invalid-token',
        ]);

        $response->assertStatus(400);
    }

    /** @test */
    public function guestCanRegisterWithValidInformation()
    {
        $response = $this->json('POST', route('register'), [
            'name' => 'Andreas',
            'email' => 'andreas@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'country' => 'DK',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'Andreas',
            'email' => 'andreas@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Andreas',
            'email' => 'andreas@example.com',
        ]);
    }

    /** @test */
    public function guestCanNotRegisterWithExistingEmail()
    {
        $user = factory(User::class)->create();

        $response = $this->json('POST', route('register'), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'country' => $user->country,
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function guestCanNotRegisterWithInsufficientInformation()
    {
        $response = $this->json('POST', route('register'), [
            'name' => 'Andreas',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function userCanChangeActiveTeam()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create());

        Passport::actingAs($user, ['view-teams']);
        $response = $this->json('POST', route('teams.change', $team->slug));

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'team_id' => $team->id,
        ]);
    }

    /** @test */
    public function userCanNotChangeActiveTeamToUnownedTeam()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();

        Passport::actingAs($user, ['view-teams']);
        $response = $this->json('POST', route('teams.change', $team->slug));

        $response->assertStatus(403);
    }

    /** @test */
    public function userCanRetrieveNotifications()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['view-notifications']);
        $responseAll = $this->json('GET', route('notifications', 'all'));
        $responseRecent = $this->json('GET', route('notifications', 'recent'));

        $responseAll->assertStatus(200);
        $responseRecent->assertStatus(200);

        // Check response data.
    }
}
