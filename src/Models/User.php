<?php

namespace ZapsterStudios\TeamPay\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Get the users active team.
     */
    public function team()
    {
        return $this->hasOne('App\Team', 'id', 'team_id');
    }

    /**
     * Get all the users teams.
     */
    public function teams()
    {
        return $this->belongsToMany('App\Team', 'team_members', 'user_id', 'team_id');
    }

    /**
     * Get all the users owned teams.
     */
    public function ownedTeams()
    {
        return $this->hasMany('App\Team', 'user_id', 'id');
    }

    /**
     * Check if user is on team.
     *
     * @param  \App\Team  $team
     * @return bool
     */
    public function onTeam($team)
    {
        return $this->teams->contains($team);
    }

    /**
     * Check if user owns team.
     *
     * @param  \App\Team  $team
     * @return bool
     */
    public function ownsTeam($team)
    {
        return $this->id === $team->user_id;
    }
}
