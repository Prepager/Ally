<?php

namespace ZapsterStudios\Ally\Policies;

use App\Team;
use App\User;
use ZapsterStudios\Ally\Models\TeamInvitation;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamInvitationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the invitation.
     *
     * @param  \App\User  $user
     * @param  \ZapsterStudios\Ally\Models\TeamInvitation  $invitation
     * @return mixed
     */
    public function userView(User $user, TeamInvitation $invitation = null)
    {
        return $user->tokenCan('invitations.show')
            && (! $invitation || $invitation->user_id == $user->id);
    }

    /**
     * Determine whether the user can accept the invitation.
     *
     * @param  \App\User  $user
     * @param  \ZapsterStudios\Ally\Models\TeamInvitation  $invitation
     * @return mixed
     */
    public function accept(User $user, TeamInvitation $invitation)
    {
        return $user->tokenCan('invitations.update')
            && $invitation->email === $user->email;
    }

    /**
     * Determine whether the user can decline the invitation.
     *
     * @param  \App\User  $user
     * @param  \ZapsterStudios\Ally\Models\TeamInvitation  $invitation
     * @return mixed
     */
    public function decline(User $user, TeamInvitation $invitation)
    {
        return $user->tokenCan('invitations.update')
            && $invitation->email === $user->email;
    }

    /**
     * Determine whether the user can view the invitation.
     *
     * @param  \App\User  $user
     * @param  \ZapsterStudios\Ally\Models\TeamInvitation  $invitation
     * @return mixed
     */
    public function view(User $user, TeamInvitation $invitation = null)
    {
        $team = ($invitation ? $invitation->team : null);

        return $user->tokenCan('teams.show')
            && (! $team || $user->onTeam($team))
            && (! $invitation || $invitation->team_id == $team->id);
    }

    /**
     * Determine whether the user can create invite new member.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        $team = request()->route('team');

        return $user->tokenCan('teams.members.create')
            && $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can update the invitation.
     *
     * @param  \App\User  $user
     * @param  \ZapsterStudios\Ally\Models\TeamInvitation  $invitation
     * @return mixed
     */
    public function update(User $user, TeamInvitation $invitation)
    {
        $team = $invitation->team;

        return $user->tokenCan('teams.members.update')
            && $user->ownsTeam($team)
            && $invitation->team_id == $team->id;
    }

    /**
     * Determine whether the user can delete the invitation.
     *
     * @param  \App\User  $user
     * @param  \ZapsterStudios\Ally\Models\TeamInvitation  $invitation
     * @return mixed
     */
    public function delete(User $user, TeamInvitation $invitation)
    {
        $team = $invitation->team;

        return $user->tokenCan('teams.members.delete')
            && $user->ownsTeam($team)
            && $invitation->team_id == $team->id;
    }
}
