<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\User' => 'ZapsterStudios\TeamPay\Policies\UserPolicy',
        'App\Team' => 'ZapsterStudios\TeamPay\Policies\TeamPolicy',

        'ZapsterStudios\TeamPay\Models\TeamMember' => 'ZapsterStudios\TeamPay\Policies\TeamMemberPolicy',
        'ZapsterStudios\TeamPay\Models\TeamInvitation' => 'ZapsterStudios\TeamPay\Policies\TeamInvitationPolicy',

        'Illuminate\Notifications\Notification' => 'ZapsterStudios\TeamPay\Policies\NotificationPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
