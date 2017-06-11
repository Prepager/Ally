<?php

namespace App;

use ZapsterStudios\TeamPay\Models\Team as TeamPayTeam;

class Team extends TeamPayTeam
{
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
        'braintree_id', 'paypal_email', 'card_brand', 'card_last_four',
    ];

    /**
     * The model validation rules.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|min:2',
    ];
}
