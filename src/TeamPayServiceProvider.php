<?php

namespace ZapsterStudios\TeamPay;

use Carbon\Carbon;
use Laravel\Passport\Passport;
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

        $this->app->make('Illuminate\Database\Eloquent\Factory')->load(__DIR__.'/Database/Factories');

        $this->bootPassport();
        $this->bootBraintree();
    }

    /**
     * Boot the passport settings.
     *
     * @return void
     */
    public function bootPassport()
    {
        Passport::tokensExpireIn(Carbon::now()->addMinutes(30));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));

        Passport::tokensCan([
            // User
            'view-notifications' => 'Read Notifications',

            // Teams
            'view-teams' => 'View Teams',
            'manage-teams' => 'Manage Teams and its Members',

            //
        ]);
    }

    /**
     * Boot the Braintree settings.
     *
     * @return void
     */
    public function bootBraintree()
    {
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

        $this->recordQueries();
    }

    /**
     * Record database queries.
     *
     * @return void
     */
    public function recordQueries()
    {
        if (! env('DB_LOGGER')) {
            return;
        }

        \DB::listen(function ($query) {
            foreach ($query->bindings as $index => $binding) {
                if ($binding instanceof \DateTime) {
                    $query->bindings[$index] = $binding->format('\'Y-m-d H:i:s\'');
                }
            }

            array_push(\TeamPay::$queryLog, [
                vsprintf(str_replace(['%', '?'], ['%%', '%s'], $query->sql), $query->bindings),
                $query->time,
            ]);
        });
    }
}
