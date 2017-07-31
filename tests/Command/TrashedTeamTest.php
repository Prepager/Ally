<?php

namespace ZapsterStudios\Ally\Tests\Command;

use Ally;
use App\Team;
use ZapsterStudios\Ally\Tests\TestCase;

class TrashedTeamTest extends TestCase
{
    /**
     * @test
     * @group Command
     */
    public function skipGraceDoesNotPerformActions()
    {
        Ally::$skipDeletionGracePeriod = true;

        $team = factory(Team::class)->create([
            'deleted_at' => '2017-01-01 00:00:00',
        ]);

        $this->artisan('teams:clean');

        $this->assertSoftDeleted('teams', [
            'id' => $team->id,
        ]);
    }

    /**
     * @test
     * @group Command
     */
    public function trashedTeamAboveLimitIsDeleate()
    {
        Ally::$skipDeletionGracePeriod = false;

        $team = factory(Team::class)->create([
            'deleted_at' => '2017-01-01 00:00:00',
        ]);

        $this->artisan('teams:clean');

        $this->assertDatabaseMissing('teams', [
            'id' => $team->id,
        ]);
    }
}
