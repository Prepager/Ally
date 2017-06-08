<?php

namespace ZapsterStudios\TeamPay;

use Illuminate\Support\ServiceProvider;

class TeamPayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the package services.
     *
     * @return void
     */
    public function boot()
    {
        class_alias('ZapsterStudios\TeamPay\TeamPay', 'TeamPay');
        
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');
        
        if ($this->app->runningInConsole()) {
            $this->commands([
                //
            ]);
        }
    }
}