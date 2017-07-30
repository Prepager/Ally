<?php

namespace ZapsterStudios\Ally\Tests\Data;

use Ally;
use ZapsterStudios\Ally\Tests\TestCase;

class ResponseTest extends TestCase
{
    /**
     * @test
     * @group Data
     */
    public function canRetrieveResponseMessages()
    {
        $this->assertSame(Ally::getResponseMessage(404), 'Not Found');
        $this->assertSame(Ally::getResponseMessages()[404], 'Not Found');
    }
}
