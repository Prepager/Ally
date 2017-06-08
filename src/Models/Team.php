<?php

namespace ZapsterStudios\TeamPay\Models;

use Laravel\Cashier\Billable;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use Billable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'slug',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'braintree_id',
        'paypal_email',
        'card_brand',
        'card_last_four',
    ];

    /**
     * Get the current team owner.
     */
    public function owner()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get all the team members.
     */
    public function members()
    {
        // TODO
    }
}
