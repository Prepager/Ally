<?php

namespace ZapsterStudios\Ally\Tests\Account;

use Ally;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use ZapsterStudios\Ally\Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use ZapsterStudios\Ally\Events\Users\UserCreated;
use ZapsterStudios\Ally\Notifications\EmailVerification;

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
            'team' => 'My Unique Team',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'Andreas',
            'email' => 'andreas@example.com',
            'team' => [
                'name' => 'My Unique Team',
            ],
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Andreas',
            'email' => 'andreas@example.com',
        ]);

        $this->assertDatabaseHas('teams', [
            'name' => 'My Unique Team',
        ]);

        Event::assertDispatched(UserCreated::class, function ($e) {
            return $e->user->email == 'andreas@example.com';
        });

        $user = User::findOrFail(json_decode($response->getContent())->id);
        Notification::assertSentTo($user, EmailVerification::class,
            function ($notification, $channels) use ($user) {
                $data = $notification->toMail($user)->toArray();

                $this->assertSame($data['actionUrl'], str_replace('{token}', $notification->token, Ally::$linkAccountVerification));
                $this->assertSame($notification->user->name, $user->name);
                $this->assertSame($notification->token, $user->email_token);

                return true;
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
    public function userCanNotUpdateAvatarWithoutImage()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, ['user.update']);
        $response = $this->json('POST', route('account.avatar.update'));

        $response->assertStatus(422);
    }

    /**
     * @test
     * @group Account
     */
    public function userCanUpdateAvatar()
    {
        Storage::fake('public');

        $user = factory(User::class)->create();

        Passport::actingAs($user, ['user.update']);
        $response = $this->json('POST', route('account.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertStatus(200);

        $this->assertTrue($user->getOriginal('avatar') !== null);
        Storage::disk('public')->assertExists($user->getOriginal('avatar'));

        $avatar = $user->getOriginal('avatar');
        $response = $this->json('POST', route('account.avatar.update'), [
            'avatar' => UploadedFile::fake()->image('new-avatar.jpg'),
        ]);

        $response->assertStatus(200);

        $this->assertTrue($user->getOriginal('avatar') !== null);
        Storage::disk('public')->assertExists($user->getOriginal('avatar'));
        Storage::disk('public')->assertMissing($avatar);
    }
}
