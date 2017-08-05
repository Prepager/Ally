<?php

namespace ZapsterStudios\Ally\Tests\Account;

use App\Team;
use App\User;
use Laravel\Passport\Passport;
use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Tests\Stubs\DatabaseNotification;

class NotificationTest extends TestCase
{
    /**
     * @test
     * @group Account
     */
    public function userCanRetrieveAllAndRecentNotifications()
    {
        $user = factory(User::class)->create();

        $user->notify(new DatabaseNotification());
        $user->notify(new DatabaseNotification());

        Passport::actingAs($user, ['notifications.show']);
        $response = $this->json('GET', route('account.notifications.index', 'all'));

        $response->assertStatus(200);
        $response->assertJson([
            'total' => 2,
        ]);

        $response = $this->json('GET', route('account.notifications.index', 'recent'));

        $response->assertStatus(200);
        $this->assertCount(2, json_decode($response->getContent()));
    }

    /**
     * @test
     * @group Account
     */
    public function userCanRetrieveSingleNotification()
    {
        $user = factory(User::class)->create();

        $user->notify(new DatabaseNotification());
        $notificaton = $user->notifications()->orderBy('id', 'desc')->first();

        Passport::actingAs($user, ['notifications.show']);
        $response = $this->json('GET', route('account.notifications.show', $notificaton));

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $notificaton->id,
        ]);
    }

    /**
     * @test
     * @group Account
     */
    public function userCanChangeReadStatus()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();

        $user->notify(new DatabaseNotification());
        $notificaton = $user->notifications()->orderBy('id', 'desc')->first();

        Passport::actingAs($user, ['notifications.update']);
        $response = $this->json('PATCH', route('account.notifications.update', $notificaton), [
            'read' => true,
        ]);

        $response->assertStatus(200);
        $notificaton = $notificaton->fresh();
        $this->assertTrue($notificaton->read());

        $response = $this->json('PATCH', route('account.notifications.update', $notificaton), [
            'read' => false,
        ]);

        $response->assertStatus(200);
        $notificaton = $notificaton->fresh();
        $this->assertTrue($notificaton->unread());
    }

    /**
     * @test
     * @group Account
     */
    public function userCanDeleteNotification()
    {
        $user = factory(User::class)->create();

        $user->notify(new DatabaseNotification());
        $notificaton = $user->notifications()->orderBy('id', 'desc')->first();

        Passport::actingAs($user, ['notifications.delete']);
        $response = $this->json('DELETE', route('account.notifications.destroy', $notificaton));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('notifications', [
            'id' => $notificaton->id,
        ]);
    }
}
