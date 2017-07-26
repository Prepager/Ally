<?php

namespace ZapsterStudios\Ally\Configuration;

trait TeamConfiguration
{
    /**
     * The name used for teams (singular).
     *
     * @var string
     */
    public static $teamName = 'team';

    /**
     * Whether or not the teams should be instantly deleated.
     *
     * @var bool
     */
    public static $skipDeletionGracePeriod = false;

    /**
     * The amount of time before a trashed team should be deleated.
     *
     * @var int
     */
    public static $gracePeriodDays = 1;

    /**
     * The groups used for the teams members.
     *
     * @var array
     */
    public static $groups = [];

    /**
     * Create default groups.
     *
     * @return void
     */
    public static function teamSetup()
    {
        static::addGroup('owner', 'Owner', ['*']);
        static::addGroup('member', 'Member');
    }

    /**
     * Add a new team group.
     *
     * @param  string  $id
     * @param  string  $name
     * @param  array  $permissions
     * @return \ZapsterStudios\Ally\Configuration\Models\Group
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
     * @return Collection
     */
    public static function groups()
    {
        return collect(static::$groups);
    }

    /**
     * Return a single group.
     *
     * @return Group
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
     * @return string
     */
    public static function inGroup()
    {
        return 'in:'.static::groups()->implode('id', ',');
    }
}
