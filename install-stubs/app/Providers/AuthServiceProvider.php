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
        'App\User' => 'ZapsterStudios\Ally\Policies\UserPolicy',
        'App\Team' => 'ZapsterStudios\Ally\Policies\TeamPolicy',

        'ZapsterStudios\Ally\Models\TeamMember' => 'ZapsterStudios\Ally\Policies\TeamMemberPolicy',
        'ZapsterStudios\Ally\Models\TeamInvitation' => 'ZapsterStudios\Ally\Policies\TeamInvitationPolicy',

        'Illuminate\Notifications\Notification' => 'ZapsterStudios\Ally\Policies\NotificationPolicy',
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
