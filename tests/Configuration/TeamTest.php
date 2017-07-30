<?php

namespace ZapsterStudios\Ally\Tests\Configuration;

use Ally;
use ZapsterStudios\Ally\Tests\TestCase;

class TeamTest extends TestCase
{
    /**
     * @test
     * @group Configuration
     */
    public function canAddGroup()
    {
        Ally::addGroup('some-group', 'Some Group', ['*']);
        $group = collect(Ally::$groups)->first(function($info) {
            return $info->id == 'some-group';
        });

        $this->assertTrue($group !== null);
        $this->assertSame($group->name, 'Some Group');
        $this->assertSame($group->permissions, ['*']);

        $group->rename('Some Other Group');
        $this->assertSame($group->name, 'Some Other Group');

        $permissions = [
            'new-permission' => true,
        ];
        $group->permissions($permissions);
        $this->assertSame($group->permissions, $permissions);
    }

    /**
     * @test
     * @group Configuration
     */
    public function canRetrieveGroups()
    {
        $this->assertSame(Ally::groups()->all(), Ally::$groups);
        $this->assertSame(Ally::group(Ally::$groups[1]->id), Ally::$groups[1]);
    }
}
