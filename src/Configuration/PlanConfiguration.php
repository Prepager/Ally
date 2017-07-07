<?php

namespace ZapsterStudios\TeamPay\Configuration;

trait PlanConfiguration
{
    /**
     * The plans available.
     *
     * @var array
     */
    public static $plans = [];

    /**
     * Add a new plan.
     */
    public static function addPlan($id, $name, $price = 0)
    {
        $plan = new Models\Plan($id, $name, $price);
        static::$plans[] = $plan;

        return $plan;
    }

    /**
     * Duplicate an existing plan.
     */
    public static function duplicatePlan($duplicate, $id, $name = null, $price = null)
    {
        $existing = static::plans()->where('id', $duplicate)->first();
        if(! $existing) {
            throw new \Exception('Unable to duplicate plan ('.$duplicate.').');
        }

        $plan = clone $existing;
        $plan->__construct($id, ($name ?? $plan->name), ($price ?? $plan->price));

        static::$plans[] = $plan;

        return $plan;
    }

    /**
     * Return all plans.
     *
     * @returns array
     */
    public static function plans()
    {
        return collect(static::$plans)->sortBy('price');
    }

    /**
     * Return single plan.
     *
     * @returns array
     */
    public static function plan($id)
    {
        return static::plans()->first(function ($plan) use ($id) {
            return $plan->id === $id;
        });
    }

    /**
     * Return free plans.
     *
     * @returns array
     */
    public static function freePlans()
    {
        return static::plans()->reject(function ($value) {
            return $value->price;
        });
    }

    /**
     * Return the default free plan.
     *
     * @returns array
     */
    public static function freePlan()
    {
        return static::freePlans()->first();
    }

    /**
     * Return active plans.
     *
     * @returns array
     */
    public static function activePlans()
    {
        return static::plans()->reject(function ($value) {
            return ! $value->active;
        });
    }

    /**
     * Return active plan ids.
     *
     * @returns array
     */
    public static function activePlanIDs()
    {
        return static::activePlans()->map(function ($plan) {
            return $plan->id;
        });
    }

    /**
     * Return archived plans.
     *
     * @returns array
     */
    public static function archivedPlans()
    {
        return static::plans()->reject(function ($value) {
            return $value->active;
        });
    }
}
