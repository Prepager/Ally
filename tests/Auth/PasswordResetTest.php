<?php

namespace ZapsterStudios\Ally\Tests\Auth;

use Ally;
use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use ZapsterStudios\Ally\Models\PasswordReset;
use ZapsterStudios\Ally\Notifications\PasswordReset as PasswordResetMail;

class PasswordResetTest extends TestCase
{
    /**
     * @test
     * @group Auth
     */
    public function userCanNotRequestReset()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, []);
        $response = $this->json('POST', route('login.reset.store'));

        $response->assertStatus(403);
    }

    /**
     * @test
     * @group Auth
     */
    public function guestCanNotRequestResetWithInvalidEmail()
    {
        $response = $this->json('POST', route('login.reset.store'), [
            'email' => 'non-existing@example.com',
        ]);

        $response->assertStatus(404);
    }

    /**
     * @test
     * @group Auth
     */
    public function guestCanRequestResetWithValidEmail()
    {
        Notification::fake();

        $user = factory(User::class)->create();

        $response = $this->json('POST', route('login.reset.store'), [
            'email' => $user->email,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('password_resets', [
            'email' => $user->email,
        ]);

        $request = PasswordReset::orderBy('email', $user->email)->firstOrFail();

        Notification::assertSentTo($user, PasswordResetMail::class,
            function ($notification, $channels) use ($user, $request) {
                $data = $notification->toMail($user)->toArray();

                $this->assertSame($data['actionUrl'], str_replace('{token}', $notification->token, Ally::$linkPasswordReset));
                $this->assertSame($notification->user->name, $user->name);
                $this->assertSame($notification->token, $request->token);

                return true;
            }
        );
    }

    /**
     * @test
     * @group Auth
     */
    public function guestCanNotResetWithInvalidToken()
    {
        $request = factory(PasswordReset::class)->create();

        $response = $this->json('PATCH', route('login.reset.update', 'no-token'), [
            'password' => 'new-secret',
            'password_confirmation' => 'new-secret',
        ]);

        $response->assertStatus(404);
    }

    /**
     * @test
     * @group Auth
     */
    public function guestCanResetWithValidToken()
    {
        $user = factory(User::class)->create();
        $password = $user->password;
        $request = factory(PasswordReset::class)->create([
            'email' => $user->email,
        ]);

        $response = $this->json('PATCH', route('login.reset.update', $request->token), [
            'email' => $user->email,
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
