<?php

namespace ZapsterStudios\TeamPay\Tests\Feature;

use TeamPay;
use App\User;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Event;
use ZapsterStudios\TeamPay\Tests\TestCase;
use ZapsterStudios\TeamPay\Models\Announcement;
use ZapsterStudios\TeamPay\Events\Announcements\AnnouncementCreated;

class AnnouncementTest extends TestCase
{
    /** @test */
    public function guestCanRetrieveRecentAnnouncements()
    {
        factory(Announcement::class, 2)->create();

        $response = $this->json('GET', route('announcements.index', 'recent'));

        $response->assertStatus(200);
        $this->assertCount(2, $response->getData());
    }

    /** @test */
    public function guestCanRetrieveAllAnnouncements()
    {
        factory(Announcement::class, 2)->create();

        $response = $this->json('GET', route('announcements.index', 'all'));

        $response->assertStatus(200);
        $this->assertEquals(2, $response->getData()->total);
    }

    /** @test */
    public function guestCanRetrieveSingleAnnouncement()
    {
        $announcement = factory(Announcement::class)->create();

        $response = $this->json('GET', route('announcements.show', $announcement->id));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => $announcement->message,
        ]);
    }

    /** @test */
    public function guestCanNotCreateAnnouncement()
    {
        $response = $this->json('POST', route('announcements.store'), [
            'message' => 'Test',
            'visit' => '#',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function adminCanCreateAnnouncement()
    {
        Event::fake();

        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        Passport::actingAs($user, ['manage-application']);
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

    /** @test */
    public function adminCanUpdateAnnouncement()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        $announcement = factory(Announcement::class)->create();

        Passport::actingAs($user, ['manage-application']);
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

    /** @test */
    public function adminCanDeleteAnnouncement()
    {
        $user = factory(User::class)->create();
        TeamPay::setAdmins([$user->email]);

        $announcement = factory(Announcement::class)->create();

        Passport::actingAs($user, ['manage-application']);
        $response = $this->json('DELETE', route('announcements.destroy', $announcement->id));

        $response->assertStatus(200);

        $this->assertDatabaseMissing('announcements', [
            'id' => $announcement->id,
        ]);
    }
}
