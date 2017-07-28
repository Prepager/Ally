<?php

namespace ZapsterStudios\Ally\Models;

use Ally;
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
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function team()
    {
        return $this->belongsTo(Ally::teamModel());
    }

    /**
     * Get the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Ally::userModel());
    }
}
