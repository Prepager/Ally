<?php

namespace ZapsterStudios\Ally\Providers;

use Illuminate\Support\ServiceProvider;

class ExtendedServiceProvider extends ServiceProvider
{
    /**
     * Load factories from directory.
     *
     * @param  string  $path
     *
     * @return void
     */
    protected function loadFactoriesFrom($path)
    {
        $this->app->make('Illuminate\Database\Eloquent\Factory')->load($path);
    }

    /**
     * Register class alias.
     *
     * @param  string  $path
     * @param  string  $name
     * @param  function $callback
     *
     * @return void
     */
    protected function registerAlias($path, $name, $callback)
    {
        if (! class_exists($name)) {
            class_alias($path, $name);

            $callback();
        }
    }

    /**
     * Register scheduled commands.
     *
     * @param  array  $commands
     *
     * @return void
     */
    protected function registerCommands($commands)
    {
        if ($this->app->runningInConsole()) {
            $this->commands($commands);
        }
    }
}
