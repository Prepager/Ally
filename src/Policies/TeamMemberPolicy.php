<?php

namespace ZapsterStudios\Ally\Policies;

use App\Team;
use App\User;
use ZapsterStudios\Ally\Models\TeamMember;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamMemberPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view a team member.
     *
     * @param  \App\User  $user
     * @param  \ZapsterStudios\Ally\Models\TeamMember  $team
     * @return mixed
     */
    public function view(User $user, TeamMember $member = null)
    {
        $team = ($member ? $member->team : null);

        return $user->tokenCan('teams.show')
            && (! $team || $user->onTeam($team))
            && (! $member || $member->team_id === $team->id);
    }

    /**
     * Determine whether the user can update the teamMember.
     *
     * @param  \App\User  $user
     * @param  \App\Team  $team
     * @param  \ZapsterStudios\Ally\Models\TeamMember  $member
     * @return mixed
     */
    public function update(User $user, TeamMember $member)
    {
        $team = $member->team;

        return $user->tokenCan('teams.members.update')
            && $user->ownsTeam($team)
            && $member->team_id === $team->id;
    }

    /**
     * Determine whether the user can delete the teamMember.
     *
     * @param  \App\User  $user
     * @param  \App\Team  $team
     * @param  \ZapsterStudios\Ally\Models\TeamMember  $member
     * @return mixed
     */
    public function delete(User $user, TeamMember $member)
    {
        $team = $member->team;

        return $user->tokenCan('teams.members.delete')
            && $user->ownsTeam($team)
            && $member->team_id === $team->id;
    }
}
