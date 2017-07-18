<?php

namespace ZapsterStudios\TeamPay\Configuration;

trait TeamConfiguration
{
    /**
     * The name used for teams (singular).
     *
     * @var string
     */
    public static $teamName = 'team';

    /**
     * The groups used for the teams members.
     *
     * @var array
     */
    public static $groups = [];

    /**
     * Create default groups.
     *
     * @returns void
     */
    public static function teamSetup()
    {
        static::addGroup('owner', 'Owner', ['*']);
        static::addGroup('member', 'Member');
    }

    /**
     * Add a new team group.
     *
     * @returns Group
     */
    public static function addGroup($id, $name, $permissions = [])
    {
        $group = new Models\Group($id, $name, $permissions);
        static::$groups[] = $group;

        return $group;
    }

    /**
     * Return all groups.
     *
     * @returns collection
     */
    public static function groups()
    {
        return collect(static::$groups);
    }

    /**
     * Return a single group.
     *
     * @returns Group
     */
    public static function group($id)
    {
        return static::groups()->first(function ($group) use ($id) {
            return $group->id === $id;
        });
    }

    /**
     * Return a in group validation.
     *
     * @returns string
     */
    public static function inGroup()
    {
        return 'in:'.static::groups()->implode('id', ',');
    }
}
