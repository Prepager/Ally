<?php

// Patterns
Route::pattern('method', 'recent|all');

// Group: Third-Party
Route::group(['middleware' => ['api', 'suspended']], function () {

    // Laravel: Passport
    \Laravel\Passport\Passport::routes();

    // Laravel: Cashier
    Route::post('/braintree/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');
});

// Group: API
Route::group([
    'middleware' => ['api', 'suspended'],
    'namespace' => 'ZapsterStudios\Ally\Controllers',
], function () {

    // Group: Public
    Route::group([], function () {

        // App
        Route::get('/app', 'AppController@index')->name('app');
        Route::get('/app/routes', 'AppController@routes')->name('app.routes');

        // Account
        Route::post('/account/verify/{token}', 'Account\AccountController@verify')->name('account.verify');

        // Auth
        Route::post('/login/refresh', 'Auth\AuthController@refresh')->name('refresh');

        // Announcements
        Route::get('/announcements/{method?}', 'AnnouncementController@index')->name('announcements.index');
        Route::get('/announcements/{announcement}', 'AnnouncementController@show')->name('announcements.show');
    });

    // Group: Unauthenticated
    Route::group(['middleware' => 'unauthenticated'], function () {

        // Auth
        Route::post('/login', 'Auth\AuthController@login')->name('login');

        // Password-Reset
        Route::post('/login/reset', 'Auth\PasswordResetController@store')->name('login.reset.store');
        Route::patch('/login/reset/{reset}', 'Auth\PasswordResetController@update')->name('login.reset.update');

        // Account
        Route::post('/register', 'Account\AccountController@store')->name('account.store');
    });

    // Group: Authenticated
    Route::group(['middleware' => 'auth:api'], function () {

        // Variables
        $plural = str_plural(Ally::$teamName);

        // Auth
        Route::post('/logout', 'Auth\AuthController@logout')->name('logout');

        // Account
        Route::get('/account', 'Account\AccountController@show')->name('account.show');
        Route::post('/account', 'Account\AccountController@update')->name('account.update');
        Route::patch('/account/password', 'Account\PasswordController@update')->name('account.password.update');

        // Account Avatar
        Route::post('/account/avatar', 'Account\AvatarController@update')->name('account.avatar.update');
        Route::delete('/account/avatar', 'Account\AvatarController@destroy')->name('account.avatar.destroy');

        // Notifications
        Route::get('/account/notifications/{method?}', 'Account\NotificationController@index')->name('account.notifications.index');
        Route::get('/account/notifications/{notifications}', 'Account\NotificationController@show')->name('account.notifications.show');
        Route::patch('/account/notifications/{notification}', 'Account\NotificationController@update')->name('account.notifications.update');
        Route::delete('/account/notifications/{notification}', 'Account\NotificationController@destroy')->name('account.notifications.destroy');

        // Invitations
        Route::get('/account/invitations', 'Account\InvitationController@index')->name('account.invitations.index');
        Route::patch('/account/invitations/{invitation}', 'Account\InvitationController@update')->name('account.invitations.update');
        Route::delete('/account/invitations/{invitation}', 'Account\InvitationController@destroy')->name('account.invitations.destroy');

        // Teams
        Route::get($plural, 'Team\TeamController@index')->name('teams.index');
        Route::post($plural, 'Team\TeamController@store')->name('teams.store');

        // Group: Teams
        Route::group(['prefix' => $plural.'/{team}'], function () {

            // Teams
            Route::get('/', 'Team\TeamController@show')->name('teams.show');
            Route::patch('/', 'Team\TeamController@update')->name('teams.update');
            Route::delete('/', 'Team\TeamController@destroy')->name('teams.destroy');
            Route::post('/change', 'Team\TeamController@change')->name('teams.change');
            Route::post('/restore', 'Team\TeamController@restore')->name('teams.restore');

            // Teams Avatar
            Route::post('/avatar', 'Team\AvatarController@update')->name('teams.avatar.update');
            Route::delete('/avatar', 'Team\AvatarController@destroy')->name('teams.avatar.destroy');

            // Subscription
            Route::post('/subscription', 'Subscription\SubscriptionController@subscription')->name('subscription');
            Route::post('/subscription/token', 'Subscription\TokenController@store')->name('subscription.token');

            // Group: Subscribed
            Route::group(['middleware' => 'subscribed'], function () {

                // Subscription
                Route::post('/subscription/cancel', 'Subscription\SubscriptionController@cancel')->name('subscription.cancel');
                Route::post('/subscription/resume', 'Subscription\SubscriptionController@resume')->name('subscription.resume');
            });

            // Invoices
            Route::get('/invoices', 'Subscription\InvoiceController@index')->name('invoices.index');
            Route::get('/invoices/{id}', 'Subscription\InvoiceController@show')->name('invoices.show');

            // Members
            Route::get('/members', 'Team\MemberController@index')->name('teams.members.index');
            Route::get('/members/{member}', 'Team\MemberController@show')->name('teams.members.show');
            Route::patch('/members/{member}', 'Team\MemberController@update')->name('teams.members.update');
            Route::delete('/members/{member}', 'Team\MemberController@destroy')->name('teams.members.destroy');

            // Invitations
            Route::get('/invitations', 'Team\InvitationController@index')->name('teams.invitations.index');
            Route::post('/invitations', 'Team\InvitationController@store')->name('teams.invitations.store');
            Route::get('/invitations/{invitation}', 'Team\InvitationController@show')->name('teams.invitations.show');
            Route::patch('/invitations/{invitation}', 'Team\InvitationController@update')->name('teams.invitations.update');
            Route::delete('/invitations/{invitation}', 'Team\InvitationController@destroy')->name('teams.invitations.destroy');
        });

        // Group: Administrator
        Route::group(['middleware' => 'administrator'], function () {

            // Dashboard
            Route::get('/dashboard', 'Dashboard\AnalyticsController@index')->name('dashboard.index');

            // Announcements
            Route::post('/announcements', 'AnnouncementController@store')->name('announcements.store');
            Route::patch('/announcements/{announcement}', 'AnnouncementController@update')->name('announcements.update');
            Route::delete('/announcements/{announcement}', 'AnnouncementController@destroy')->name('announcements.destroy');

            // Impersonate
            Route::post('/dashboard/users/impersonate/{user}', 'Dashboard\ImpersonationController@store')->name('dashboard.users.impersonation.store');
            Route::delete('/dashboard/users/impersonate', 'Dashboard\ImpersonationController@destroy')->name('dashboard.users.impersonation.destroy');

            // Users
            Route::get('/dashboard/users', 'Dashboard\UserController@index')->name('dashboard.users.index');
            Route::get('/dashboard/users/{user}', 'Dashboard\UserController@show')->name('dashboard.users.show');
            Route::post('/dashboard/users/search', 'Dashboard\UserController@search')->name('dashboard.users.search');
            Route::post('/dashboard/users/{user}/suspend', 'Dashboard\UserSuspensionController@store')->name('dashboard.users.suspension.store');
            Route::delete('/dashboard/users/{user}/unsuspend', 'Dashboard\UserSuspensionController@destroy')->name('dashboard.users.suspension.destroy');

            // Teams
            Route::get('/dashboard/teams', 'Dashboard\TeamController@index')->name('dashboard.teams.index');
            Route::get('/dashboard/teams/{team}', 'Dashboard\TeamController@show')->name('dashboard.teams.show');
            Route::post('/dashboard/teams/search', 'Dashboard\TeamController@search')->name('dashboard.teams.search');
            Route::post('/dashboard/teams/{team}/suspend', 'Dashboard\TeamSuspensionController@store')->name('dashboard.teams.suspension.store');
            Route::delete('/dashboard/teams/{team}/unsuspend', 'Dashboard\TeamSuspensionController@destroy')->name('dashboard.teams.suspension.destroy');
        });
    });
});
