<?php

namespace ZapsterStudios\TeamPay\Tests\Feature\Account;

use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\TeamPay\Tests\TestCase;

class PasswordTest extends TestCase
{
    /** @test */
    public function guestCanNotUpdatePassword()
    {
        $response = $this->json('PUT', route('account.password.update'));

        $response->assertStatus(401);
    }

    /** @test */
    public function userCanNotUpdatePasswordWithInvalidPassword()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['manage-account']);
        $response = $this->json('PUT', route('account.password.update'), [
            'current' => 'not-my-password',
            'password' => 'new-secret',
            'password_confirmation' >= 'new-secret',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function userCanUpdatePasswordWithValidPassword()
    {
        $user = factory(User::class)->create();
        $password = $user->password;

        Passport::actingAs($user, ['manage-account']);
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
