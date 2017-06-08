<?php

namespace ZapsterStudios\TeamPay\Models;

use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use Billable;
    
    /**
     * Get the team owner.
     *
     * @return User
     */
    function owner() {
        return $this->belongsTo('App\User');
    }
    
    /**
     * Get the team members.
     *
     * @return Users
     */
    function members() {
        // TODO
    }
}
