<?php

namespace ZapsterStudios\Ally\Configuration\Models;

class Group
{
    /**
     * The group id saved in the database.
     *
     * @var string
     */
    public $id;

    /**
     * The group name displayed publicly.
     *
     * @var string
     */
    public $name;

    /**
     * The group permissions.
     *
     * @var array
     */
    public $permissions = [];

    /**
     * Apply group details.
     *
     * @param  string  $id
     * @param  string  $name
     * @param  array  $permissions
     * @return void
     */
    public function __construct($id, $name, $permissions)
    {
        $this->id = $id;
        $this->name = $name;
        $this->permissions = $permissions;
    }

    /**
     * Rename an existing group.
     *
     * @param  string  $name
     * @return self
     */
    public function rename($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the group permissions.
     *
     * @param  array  $permissions
     * @return self
     */
    public function permissions($permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }
}
