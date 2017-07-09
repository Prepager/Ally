<?php

namespace ZapsterStudios\TeamPay\Configuration;

trait UserConfiguration
{
    /**
     * The emails of the administrators.
     *
     * @var array
     */
    public static $admins = [];

    /**
     * Return all admins.
     *
     * @returns array
     */
    public static function admins()
    {
        return collect(static::$admins);
    }

    /**
     * Check if email is admin.
     *
     * @returns bool
     */
    public static function isAdmin($email)
    {
        return static::admins()->contains($email);
    }
}
