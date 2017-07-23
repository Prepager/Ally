<?php

namespace ZapsterStudios\TeamPay\Policies;

use App\Team;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the team.
     *
     * @param  \App\User  $user
     * @param  \App\Team  $team
     * @return mixed
     */
    public function view(User $user, Team $team = null)
    {
        return $user->tokenCan('teams.show')
            && (! $team || $user->onTeam($team));
    }

    /**
     * Determine whether the user can create teams.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->tokenCan('teams.create');
    }

    /**
     * Determine whether the user can update the team.
     *
     * @param  \App\User  $user
     * @param  \App\Team  $team
     * @return mixed
     */
    public function update(User $user, Team $team)
    {
        return $user->tokenCan('teams.update')
            && $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can delete the team.
     *
     * @param  \App\User  $user
     * @param  \App\Team  $team
     * @return mixed
     */
    public function delete(User $user, Team $team)
    {
        return $user->tokenCan('teams.delete')
            && $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can restore the team.
     *
     * @param  \App\User  $user
     * @param  \App\Team  $team
     * @return mixed
     */
    public function restore(User $user, Team $team)
    {
        return $user->tokenCan('teams.restore')
            && $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can manage billing for the team.
     *
     * @param  \App\User  $user
     * @param  \App\Team  $team
     * @return mixed
     */
    public function billing(User $user, Team $team)
    {
        return $user->tokenCan('teams.billing')
            && $user->ownsTeam($team);
    }

    /**
     * Determine whether the user can view invoices for the team.
     *
     * @param  \App\User  $user
     * @param  \App\Team  $team
     * @return mixed
     */
    public function invoices(User $user, Team $team)
    {
        return $user->tokenCan('teams.invoices')
            && $user->ownsTeam($team);
    }
}
