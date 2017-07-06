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
     * Return all plans.
     * 
     * @returns array
     */
    public static function plans()
    {
        return collect(static::$plans)->sortBy('price');
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
