<?php

namespace ZapsterStudios\TeamPay\Models;

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
     * The model validation rules.
     *
     * @var array
     */
    public static $rules = [
        'user_id' => 'required',
        'group' => 'required', // in array?
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
