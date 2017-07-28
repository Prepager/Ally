<?php

namespace ZapsterStudios\Ally\Configuration;

trait UserConfiguration
{
    /**
     * The emails of the administrators.
     *
     * @var array
     */
    public static $admins = [];

    /**
     * Return the team model.
     *
     * @return string
     */
    public static function userModel()
    {
        return config('auth.providers.users.model', 'App\User');
    }

    /**
     * Return all admins.
     *
     * @return Collection
     */
    public static function admins()
    {
        return collect(static::$admins);
    }

    /**
     * Set app admin emails.
     *
     * @param  array  $newAdmins
     * @return void
     */
    public static function setAdmins($newAdmins)
    {
        static::$admins = $newAdmins;
    }

    /**
     * Check if email is admin.
     *
     * @param  string  $email
     * @return bool
     */
    public static function isAdmin($email)
    {
        return static::admins()->contains($email);
    }
}
