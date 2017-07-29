<?php

namespace ZapsterStudios\Ally;

use Ally;
use Route;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Braintree_Configuration as Braintree;
use ZapsterStudios\Ally\Middleware\Suspended;
use ZapsterStudios\Ally\Middleware\Subscribed;
use ZapsterStudios\Ally\Middleware\Administrator;
use ZapsterStudios\Ally\Middleware\Unauthenticated;
use ZapsterStudios\Ally\Commands\CleanTrashedTeams;
use ZapsterStudios\Ally\Commands\InstallationCommand;

class AllyServiceProvider extends Providers\ExtendedServiceProvider
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

        $this->loadPolicies([
            'App\User' => 'ZapsterStudios\Ally\Policies\UserPolicy',
            'App\Team' => 'ZapsterStudios\Ally\Policies\TeamPolicy',
    
            'ZapsterStudios\Ally\Models\TeamMember' => 'ZapsterStudios\Ally\Policies\TeamMemberPolicy',
            'ZapsterStudios\Ally\Models\TeamInvitation' => 'ZapsterStudios\Ally\Policies\TeamInvitationPolicy',
    
            'Illuminate\Notifications\Notification' => 'ZapsterStudios\Ally\Policies\NotificationPolicy',
        ]);

        $this->loadAliasMiddlewares([
            'suspended' => Suspended::class,
            'subscribed' => Subscribed::class,
            'administrator' => Administrator::class,
            'unauthenticated' => Unauthenticated::class,
        ]);

        $this->bootPassport();
        $this->bootBraintree();
    }

    /**
     * Register the package service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAlias('ZapsterStudios\Ally\Ally', 'Ally');
        Ally::setup();

        $this->registerCommands([
            InstallationCommand::class,
            CleanTrashedTeams::class,
        ]);
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
}
