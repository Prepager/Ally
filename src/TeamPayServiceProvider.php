<?php

namespace ZapsterStudios\TeamPay;

use Illuminate\Support\ServiceProvider;
use Braintree_Configuration as Braintree;

class TeamPayServiceProvider extends ServiceProvider
{
    /**
     * Boot the package service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->loadRoutesFrom(__DIR__.'/Routes/api.php');
        
        $this->app->make('Illuminate\Database\Eloquent\Factory')->load(__DIR__ . '/Database/Factories');

        Braintree::environment(config('services.braintree.environment'));
        Braintree::merchantId(config('services.braintree.merchant_id'));
        Braintree::publicKey(config('services.braintree.public_key'));
        Braintree::privateKey(config('services.braintree.private_key'));
    }

    /**
     * Register the package service provider.
     *
     * @return void
     */
    public function register()
    {
        if (! class_exists('TeamPay')) {
            class_alias('ZapsterStudios\TeamPay\TeamPay', 'TeamPay');
        }

        if ($this->app->runningInConsole()) {
            $this->commands([
                //
            ]);
        }
    }
}
