<?php

namespace ZapsterStudios\Ally\Providers\Loaders;

use Illuminate\Support\Facades\Gate;

trait PolicyLoader
{
    /**
     * Load a single policy.
     *
     * @param  string  $class
     * @param  string  $policy
     * @return void
     */
    protected function loadPolicy($class, $policy)
    {
        Gate::policy($class, $policy);
    }

    /**
     * Load a list of policies.
     *
     * @param  array  $list
     * @return void
     */
    protected function loadPolicies($list)
    {
        collect($list)->each(function ($policy, $class) {
            $this->loadPolicy($class, $policy);
        });
    }
}
