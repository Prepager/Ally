<?php

namespace ZapsterStudios\TeamPay\Events\Announcements;

use Illuminate\Queue\SerializesModels;
use ZapsterStudios\TeamPay\Models\Announcement;

class AnnouncementCreated
{
    use SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Announcement $announcement)
    {
        $this->announcement = $announcement;
    }
}
