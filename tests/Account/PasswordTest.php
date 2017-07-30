<?php

namespace ZapsterStudios\Ally\Tests\Account;

use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;

class PasswordTest extends TestCase
{
    /**
     * @test
     * @group Account
     */
    public function guestCanNotUpdatePassword()
    {
        $response = $this->json('PUT', route('account.password.update'));

        $response->assertStatus(401);
    }

    /**
     * @test
     * @group Account
     */
    public function userCanNotUpdatePasswordWithInvalidPassword()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['user.password']);
        $response = $this->json('PUT', route('account.password.update'), [
            'current' => 'not-my-password',
            'password' => 'new-secret',
            'password_confirmation' => 'new-secret',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     * @group Account
     */
    public function userCanUpdatePasswordWithValidPassword()
    {
        $user = factory(User::class)->create();
        $password = $user->password;

        Passport::actingAs($user, ['user.password']);
        $response = $this->json('PUT', route('account.password.update'), [
            'current' => 'secret',
            'password' => 'new-secret',
            'password_confirmation' => 'new-secret',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
            'password' => $password,
        ]);
    }
}
