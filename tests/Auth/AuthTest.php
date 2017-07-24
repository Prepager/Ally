<?php

namespace ZapsterStudios\Ally\Tests\Auth;

use App\Team;
use App\User;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * @test
     * @group Auth
     */
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

    /**
     * @test
     * @group Auth
     */
    public function userCanNotLoginWithInvalidCredentials()
    {
        $response = $this->json('POST', route('login'), [
            'email' => 'unregistered@example.com',
            'password' => 'unregistered',
        ]);

        $response->assertStatus(401);
    }

    /**
     * @test
     * @group Auth
     */
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

    /**
     * @test
     * @group Auth
     */
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

    /**
     * @test
     * @group Auth
     */
    public function userCanNotRefreshTokenWithInvalidToken()
    {
        $response = $this->json('POST', route('refresh'), [
            'refresh_token' => 'invalid-token',
        ]);

        $response->assertStatus(400);
    }

    /**
     * @test
     * @group Auth
     */
    public function userCanChangeActiveTeam()
    {
        $user = factory(User::class)->create();
        $team = $user->teams()->save(factory(Team::class)->create());

        Passport::actingAs($user, ['teams.show']);
        $response = $this->json('POST', route('teams.change', $team->slug));

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'team_id' => $team->id,
        ]);
    }

    /**
     * @test
     * @group Auth
     */
    public function userCanNotChangeActiveTeamToUnownedTeam()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();

        Passport::actingAs($user, ['view-teams']);
        $response = $this->json('POST', route('teams.change', $team->slug));

        $response->assertStatus(403);
    }

    /**
     * @test
     * @group Auth
     */
    public function suspendedUserCanNotRetrieveData()
    {
        $user = factory(User::class)->create([
            'suspended_at' => Carbon::now()->subDays(1),
            'suspended_to' => Carbon::now()->addDays(1),
            'suspended_reason' => 'Some test',
        ]);

        Passport::actingAs($user, ['view-teams']);
        $response = $this->json('GET', route('teams.index'));

        $response->assertStatus(403);
        $response->assertJson([
            'suspended_at' => $user->suspended_at,
            'suspended_to' => $user->suspended_to,
            'suspended_reason' => $user->suspended_reason,
        ]);
    }

    /**
     * @test
     * @group Auth
     */
    public function expiredSuspendedUserCanRetrieveData()
    {
        $user = factory(User::class)->create([
            'suspended_at' => Carbon::now()->subDays(1),
            'suspended_to' => Carbon::now()->subMinutes(5),
            'suspended_reason' => 'Some test',
        ]);

        Passport::actingAs($user, ['teams.show']);
        $response = $this->json('GET', route('teams.index'));

        $response->assertStatus(200);
    }
}
