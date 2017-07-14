<?php

namespace ZapsterStudios\TeamPay\Tests\Feature;

use App\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Event;
use ZapsterStudios\TeamPay\Tests\TestCase;
use ZapsterStudios\TeamPay\Events\Users\UserCreated;

class UserTest extends TestCase
{
    /** @test */
    public function guestCanRegisterWithValidInformation()
    {
        Event::fake();

        $response = $this->json('POST', route('user.store'), [
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

        Event::assertDispatched(UserCreated::class, function ($e) {
            return $e->user->email == 'andreas@example.com';
        });
    }

    /** @test */
    public function guestCanNotRegisterWithExistingEmail()
    {
        $user = factory(User::class)->create();

        $response = $this->json('POST', route('user.store'), [
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
        $response = $this->json('POST', route('user.store'), [
            'name' => 'Andreas',
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function guestCanNotRetrieveUserInformation()
    {
        $response = $this->json('GET', route('user.show'));

        $response->assertStatus(401);
    }

    /** @test */
    public function userCanRetrieveUserInformation()
    {
        $user = factory(User::class)->create();

        Passport::actingAs($user, []);
        $response = $this->json('GET', route('user.show'));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function userCanNotUpdateUserInformationWithExistinEmail()
    {
        $user = factory(User::class)->create();
        $extra = factory(User::class)->create();

        Passport::actingAs($user, []);
        $response = $this->json('POST', route('user.update'), [
            'email' => $extra->email,
        ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function userCanUpdateUserInformationWithValidInformation()
    {
        $user = factory(User::class)->create([
            'name' => 'John Doe',
            'email_verified' => 1,
        ]);

        Passport::actingAs($user, []);
        $response = $this->json('POST', route('user.update'), [
            'email' => 'newmail@example.com',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'name' => 'John Doe',
            'email' => 'newmail@example.com',
            'email_verified' => 0,
        ]);
    }
}
