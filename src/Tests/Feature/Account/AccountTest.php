<?php

namespace ZapsterStudios\TeamPay\Tests\Feature\Account;

use App\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Event;
use ZapsterStudios\TeamPay\Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use ZapsterStudios\TeamPay\Events\Users\UserCreated;
use ZapsterStudios\TeamPay\Notifications\EmailVerification;

class AccountTest extends TestCase
{
    /**
     * @test
     * @group Account
     */
    public function guestCanNotRegisterWithExistingEmail()
    {
        $user = factory(User::class)->create();

        $response = $this->json('POST', route('account.store'), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'country' => $user->country,
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     * @group Account
     */
    public function guestCanNotRegisterWithInsufficientInformation()
    {
        $response = $this->json('POST', route('account.store'), [
            'name' => 'Andreas',
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     * @group Account
     */
    public function guestCanNotRetrieveUserInformation()
    {
        $response = $this->json('GET', route('account.show'));

        $response->assertStatus(401);
    }

    /**
     * @test
     * @group Account
     */
    public function guestCanRegisterWithValidInformation()
    {
        Event::fake();
        Notification::fake();

        $response = $this->json('POST', route('account.store'), [
            'name' => 'Andreas',
            'email' => 'andreas@example.com',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'country' => 'DK',
            'team' => 'Some Team',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'Andreas',
            'email' => 'andreas@example.com',
            'team' => [
                'name' => 'Some Team',
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Andreas',
            'email' => 'andreas@example.com',
        ]);

        $this->assertDatabaseHas('teams', [
            'name' => 'Some Team',
        ]);

        Event::assertDispatched(UserCreated::class, function ($e) {
            return $e->user->email == 'andreas@example.com';
        });

        $user = User::findOrFail(json_decode($response->getContent())->id);
        Notification::assertSentTo($user, EmailVerification::class,
            function ($notification, $channels) use ($user) {
                return $notification->user->name === $user->name &&
                    $notification->token === $user->email_token;
            }
        );
    }

    /**
     * @test
     * @group Account
     */
    public function guestCanNotVerifyAccountWithInvalidToken()
    {
        $response = $this->json('POST', route('account.verify', 'invalid-token'));

        $response->assertStatus(404);
    }

    /**
     * @test
     * @group Account
     */
    public function guestCanVerifyAccount()
    {
        $token = str_random(16);
        $user = factory(User::class)->create([
            'email_verified' => 0,
            'email_token' => $token,
        ]);

        $this->assertFalse($user->isVerified());

        $response = $this->json('POST', route('account.verify', $token));

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email_verified' => 1,
            'email_token' => null,
        ]);

        $user = $user->fresh();
        $this->assertTrue($user->isVerified());
    }

    /**
     * @test
     * @group Account
     */
    public function userCanRetrieveUserInformation()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['user.show']);
        $response = $this->json('GET', route('account.show'));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
        ]);
    }

    /**
     * @test
     * @group Account
     */
    public function userCanNotUpdateUserInformationWithExistinEmail()
    {
        $user = factory(User::class)->create();
        $extra = factory(User::class)->create();

        Passport::actingAs($user, ['user.update']);
        $response = $this->json('POST', route('account.update'), [
            'email' => $extra->email,
        ]);

        $response->assertStatus(422);
    }

    /**
     * @test
     * @group Account
     */
    public function userCanUpdateUserInformationWithValidInformation()
    {
        $user = factory(User::class)->create([
            'name' => 'John Doe',
            'email_verified' => 1,
        ]);

        Passport::actingAs($user, ['user.update']);
        $response = $this->json('POST', route('account.update'), [
            'email' => 'newmail@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'John Doe',
            'email' => 'newmail@example.com',
            'email_verified' => 0,
        ]);
    }

    /**
     * @test
     * @group Account
     */
    public function userCanRetrieveNotifications()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['notifications.show']);
        $responseAll = $this->json('GET', route('account.notifications.index', 'all'));
        $responseRecent = $this->json('GET', route('account.notifications.index', 'recent'));

        $responseAll->assertStatus(200);
        $responseRecent->assertStatus(200);

        // Check response data.
    }
}
