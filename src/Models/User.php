<?php

namespace ZapsterStudios\TeamPay\Models;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * Get the users teams.
     */
    public function teams()
    {
        return $this->hasMany('App\Team'); // Add member teams.
    }

    /**
     * Check the users access to a team.
     */
    public function canMange($team)
    {
        return $team->user_id == $this->id; // Check for member.
    }
}
