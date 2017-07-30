<?php

namespace ZapsterStudios\Ally\Configuration\Models;

class Plan
{
    /**
     * The plan id from the provider.
     *
     * @var string
     */
    public $id;

    /**
     * The plan name displayed publicly.
     *
     * @var string
     */
    public $name;

    /**
     * The plan price (0 for free).
     *
     * @var int
     */
    public $price;

    /**
     * The current plan status (0 for archived).
     *
     * @var int
     */
    public $active = 1;

    /**
     * The max amount of members on the team (0 for unlimited).
     *
     * @var int
     */
    public $members = 0;

    /**
     * The plan features.
     *
     * @var array
     */
    public $features = [];

    /**
     * The plan permissions.
     *
     * @var array
     */
    public $permissions = [];

    /**
     * Apply plan details.
     *
     * @param  string  $id
     * @param  string  $name
     * @param  int  $price
     * @return void
     */
    public function __construct($id, $name, $price)
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }

    /**
     * Archive plan to prevent new subs.
     *
     * @return self
     */
    public function archive()
    {
        $this->active = 0;

        return $this;
    }

    /**
     * Set maximum amount of members.
     *
     * @param  int  $count
     * @return self
     */
    public function maxMembers($count)
    {
        $this->members = $count;

        return $this;
    }

    /**
     * Set the plan features.
     *
     * @param  array  $features
     * @return self
     */
    public function features($features)
    {
        $this->features = $features;

        return $this;
    }

    /**
     * Set the plan permissions.
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
