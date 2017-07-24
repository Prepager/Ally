<?php

namespace ZapsterStudios\Ally\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group',
    ];

    /**
     * Get the team.
     */
    public function team()
    {
        return $this->belongsTo('App\Team');
    }

    /**
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
