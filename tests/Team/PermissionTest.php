<?php

namespace ZapsterStudios\Ally\Tests\Team;

use Ally;
use App\Team;
use App\User;
use ZapsterStudios\Ally\Tests\TestCase;
use ZapsterStudios\Ally\Models\TeamMember;

class PermissionTest extends TestCase
{
    /**
     * @test
     * @group Team
     */
    public function teamCanRetrievePermissions()
    {
        $team = factory(Team::class)->create();

        $free = Ally::freePlan()->permissions([
            'some-test' => true,
        ]);

        $this->assertSame($team->permissions(), $free->permissions);
        $this->assertSame($team->permission('some-test'), $free->permissions['some-test']);
    }

    /**
     * @test
     * @group Team
     */
    public function userCanCheckPermissionsOnTeam()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();
        $permissions = [
            'edit-this-feature' => true,
            'edit-other-feature' => false,
        ];

        Ally::addGroup('example-group', 'Example Group', $permissions);

        TeamMember::forceCreate([
            'team_id' => $team->id,
            'user_id' => $user->id,
            'group' => 'example-group',
        ]);

        $this->assertSame($user->groupPermissions($team)->all(), $permissions);
        $this->assertSame($user->groupPermission($team, 'edit-other-feature'), $permissions['edit-other-feature']);
        $this->assertSame($user->groupCan($team, 'edit-other-feature'), $permissions['edit-other-feature']);
    }

    /**
     * @test
     * @group Team
     */
    public function userCanNotCheckPermissionsForNonMemberTeam()
    {
        $user = factory(User::class)->create();
        $team = factory(Team::class)->create();

        $this->assertSame($user->groupPermissions($team), []);
    }
}
