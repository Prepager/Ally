<?php

namespace ZapsterStudios\Ally\Providers;

use Illuminate\Support\ServiceProvider;

class ExtendedServiceProvider extends ServiceProvider
{
    use Loaders\PolicyLoader;
    use Loaders\MiddlewareLoader;

    /**
     * Make needed classes for loaders.
     *
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->router = $app->router;
    }

    /**
     * Register class alias.
     *
     * @param  string  $path
     * @param  string  $name
     * @return void
     */
    protected function registerAlias($path, $name)
    {
        if (! class_exists($name)) {
            class_alias($path, $name);
        }
    }

    /**
     * Register scheduled commands.
     *
     * @param  array  $commands
     * @return void
     */
    protected function registerCommands($commands)
    {
        if ($this->app->runningInConsole()) {
            $this->commands($commands);
        }
    }
}
