<?php

namespace ZapsterStudios\Ally\Tests\Open;

use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Models\Announcement;

class AnnouncementTest extends TestCase
{
    /**
     * @test
     * @group Open
     */
    public function guestCanRetrieveRecentAnnouncements()
    {
        factory(Announcement::class, 2)->create();

        $response = $this->json('GET', route('announcements.index', 'recent'));

        $response->assertStatus(200);
        $this->assertCount(2, $response->getData());
    }

    /**
     * @test
     * @group Open
     */
    public function guestCanRetrieveAllAnnouncements()
    {
        factory(Announcement::class, 2)->create();

        $response = $this->json('GET', route('announcements.index', 'all'));

        $response->assertStatus(200);
        $this->assertEquals(2, $response->getData()->total);
    }

    /**
     * @test
     * @group Open
     */
    public function guestCanRetrieveSingleAnnouncement()
    {
        $announcement = factory(Announcement::class)->create();

        $response = $this->json('GET', route('announcements.show', $announcement->id));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => $announcement->message,
        ]);
    }
}
