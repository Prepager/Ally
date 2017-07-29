<?php

namespace ZapsterStudios\Ally\Providers\Loaders;

trait MiddlewareLoader
{
    /**
     * Load single alias middleware.
     *
     * @param  string  $key
     * @param  string  $file
     * @return void
     */
    protected function loadAliasMiddleware($key, $file)
    {
        $this->router->aliasMiddleware($key, $file);
    }

    /**
     * Load alias middlewares.
     *
     * @param  array  $list
     * @return void
     */
    protected function loadAliasMiddlewares($list)
    {
        collect($list)->each(function ($file, $key) {
            $this->loadAliasMiddleware($key, $file);
        });
    }
}
