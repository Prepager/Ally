<?php

namespace ZapsterStudios\TeamPay\Configuration\Models;

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
     * @returns void
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
     * @returns Group
     */
    public function rename($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Set the group permissions.
     */
    public function permissions($permissions)
    {
        $this->permissions = $permissions;

        return $this;
    }
}
