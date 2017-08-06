<?php

namespace ZapsterStudios\Ally\Tests\Open;

use ZapsterStudios\Ally\Tests\TestCase;

class AppDataTest extends TestCase
{
    /**
     * @test
     * @group Open
     */
    public function guestCanRetrieveAppConfig()
    {
        $response = $this->json('GET', route('app'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'plans', 'groups', // Reset of config options.
        ]);
    }

    /**
     * @test
     * @group Open
     */
    public function guestCanRetrieveRoutes()
    {
        $response = $this->json('GET', route('app.routes'));

        $response->assertStatus(200);
        $response->assertJson([
            'app' => [
                'method' => 'GET',
                'url' => 'app',
            ],
            'app.routes' => [
                'method' => 'GET',
                'url' => 'app/routes',
            ],
        ]);
    }
}
