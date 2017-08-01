<?php

namespace ZapsterStudios\Ally\Tests\Command;

use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Commands\Publish\Publisher;

class PublisherTest extends TestCase
{
    /**
     * Set up the publisher instance.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->command = new Command();
        $this->publisher = new Publisher($this->command, true);
    }

    /**
     * @test
     * @group Command
     */
    public function canCheckForFileExistence()
    {
        $this->assertFalse($this->publisher->exists('missing-file.php'));
        $this->assertSame(count($this->command->events['comment']), 1);
        $this->assertRegExp('/File missing/', $this->command->events['comment'][0]);

        $this->command->resetEvents();

        $this->assertTrue($this->publisher->exists($this->publisher->stubs('routes/api.php')));
        $this->assertSame(count($this->command->events['comment']), 0);
    }

    /**
     * @test
     * @group Command
     */
    public function canGetNotification()
    {
        $this->publisher->moved = true;
        $this->publisher->notify('test');
        $this->assertSame(count($this->command->events['info']), 1);
        $this->assertRegExp('/test/', $this->command->events['info'][0]);

        $this->publisher->moved = false;
        $this->publisher->notify('test');
        $this->assertSame(count($this->command->events['error']), 1);
        $this->assertRegExp('/test/', $this->command->events['error'][0]);
    }

    /**
     * @test
     * @group Command
     */
    public function canNotMoveOrAppendNonExistingFile()
    {
        $this->assertFalse($this->publisher->move('missing-file.php', '/', 'missing-file.php'));
    
        $this->assertFalse($this->publisher->append('missing-file.php', '/', 'missing-file.php'));
    }
}
