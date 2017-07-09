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
     * Set app admin emails.
     *
     * @returns void
     */
    public static function setAdmins($newAdmins)
    {
        static::$admins = $newAdmins;
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
