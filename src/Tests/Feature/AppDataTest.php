<?php

namespace ZapsterStudios\Ally\Tests\Feature;

use ZapsterStudios\Ally\Tests\TestCase;

class AppDataTest extends TestCase
{
    /** @test */
    public function guestCanRetrieveAppConfig()
    {
        $response = $this->json('GET', route('app'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'plans', 'groups', // Reset of config options.
        ]);
    }
}
