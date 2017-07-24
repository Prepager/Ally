<?php

namespace ZapsterStudios\Ally\Tests\Dashboard;

use Ally;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Event;
use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Models\Announcement;
use ZapsterStudios\Ally\Events\Announcements\AnnouncementCreated;

class AnnouncementTest extends TestCase
{
    /**
     * @test
     * @group Dashboard
     */
    public function guestCanNotCreateAnnouncement()
    {
        $response = $this->json('POST', route('announcements.store'), [
            'message' => 'Test',
            'visit' => '#',
        ]);

        $response->assertStatus(401);
    }

    /**
     * @test
     * @group Dashboard
     */
    public function adminCanCreateAnnouncement()
    {
        Event::fake();

        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('POST', route('announcements.store'), [
            'message' => 'Some message here',
            'visit' => '#',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Some message here',
        ]);

        $this->assertDatabaseHas('announcements', [
            'message' => 'Some message here',
        ]);

        Event::assertDispatched(AnnouncementCreated::class, function ($e) {
            return $e->announcement->message == 'Some message here';
        });
    }

    /**
     * @test
     * @group Dashboard
     */
    public function adminCanUpdateAnnouncement()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        $announcement = factory(Announcement::class)->create();

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('PUT', route('announcements.update', $announcement->id), [
            'message' => 'Some other message here',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Some other message here',
        ]);

        $this->assertDatabaseHas('announcements', [
            'id' => $announcement->id,
            'message' => 'Some other message here',
        ]);
    }

    /**
     * @test
     * @group Dashboard
     */
    public function adminCanDeleteAnnouncement()
    {
        $user = factory(User::class)->states('verified')->create();
        Ally::setAdmins([$user->email]);

        $announcement = factory(Announcement::class)->create();

        Passport::actingAs($user, ['user.admin']);
        $response = $this->json('DELETE', route('announcements.destroy', $announcement->id));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('announcements', [
            'id' => $announcement->id,
        ]);
    }
}
