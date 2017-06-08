<?php

namespace ZapsterStudios\TeamPay;

use Laravel\Passport\Passport;
use Braintree_Configuration as Braintree;
use Illuminate\Support\ServiceProvider;

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
        class_alias('ZapsterStudios\TeamPay\TeamPay', 'TeamPay');

        if ($this->app->runningInConsole()) {
            $this->commands([
                //
            ]);
        }
    }
}
