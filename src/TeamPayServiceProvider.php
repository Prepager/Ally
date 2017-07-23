<?php

namespace ZapsterStudios\TeamPay;

use TeamPay;
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
            'user.admin' => 'Manage application.',

            'user.show' => 'Read user information including name and email.',
            'user.update' => 'Update user information excluding password.',
            'user.password' => 'Update user password.',

            'notifications.show' => 'Read user notifications.',
            'notifications.update' => 'Mark notifications as read and unread.',
            'notifications.delete' => 'Delete user notification.',

            'invitations.show' => 'Read team invitiations',
            'invitations.update' => 'Accept or decline team invitations.',

            'teams.show' => 'Read owned teams, member teams and invitations.',
            'teams.create' => 'Create new user owned teams.',
            'teams.update' => 'Update user owned teams.',
            'teams.delete' => 'Delete user owned teams.',
            'teams.restore' => 'Restore deleated user owned teams.',

            'teams.billing' => 'Show, create and update user owned team billing.',
            'teams.invoices' => 'Show user owned team invoices.',

            'teams.members.create' => 'Invite new members to a user owned team.',
            'teams.members.update' => 'Update member and invitation roles on a user owned team.',
            'teams.members.delete' => 'Kick members and delete invitations from a user owned team.',
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
            TeamPay::setup();
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
