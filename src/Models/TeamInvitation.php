<?php

namespace ZapsterStudios\TeamPay\Models;

use Illuminate\Database\Eloquent\Model;

class TeamInvitation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'group',
    ];

    /**
     * Get the team.
     */
    public function team()
    {
        return $this->belongsTo('App\Team');
    }
}
