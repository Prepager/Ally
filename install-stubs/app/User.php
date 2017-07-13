<?php

namespace App;

use ZapsterStudios\TeamPay\Models\User as TeamPayUser;

class User extends TeamPayUser
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'suspended_at',
        'suspended_to',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The model validation rules.
     *
     * @var array
     */
    public static $rules = [
        'name' => 'required|min:2',
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed',
        'country' => 'required',
    ];
}
