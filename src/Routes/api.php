<?php

// Patterns
Route::pattern('method', 'recent|all');

// Group: Third-Party
Route::group(['middleware' => 'api'], function () {

    // Laravel: Passport
    \Laravel\Passport\Passport::routes();

    // Laravel: Cashier
    Route::post('/braintree/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');
});

// Group: API
Route::group([
    'middleware' => 'api',
    'namespace' => 'ZapsterStudios\TeamPay\Controllers',
], function () {

    // Group: Unauthenticated
    Route::group([], function () {

        // App
        Route::get('/app', 'AppController@index')->name('app');
        Route::get('/app/routes', 'AppController@routes')->name('app.routes');

        // Auth
        Route::post('/login/refresh', 'AuthController@refresh')->name('refresh');

        // Announcements
        Route::get('/announcements/{method?}', 'AnnouncementController@index')->name('announcements.index');
        Route::get('/announcements/{announcement}', 'AnnouncementController@show')->name('announcements.show');
    });

    // Group: Guest
    Route::group(['middleware' => 'guest'], function () {

        // Auth
        Route::post('/login', 'AuthController@login')->name('login');
        //Route::post('/login/password', '');

        // User
        Route::post('/register', 'AccountController@store')->name('account.store');
    });

    // Group: Authenticated
    Route::group(['middleware' => 'auth:api'], function () {

        // Variables
        $plural = str_plural(TeamPay::$teamName);

        // App
        Route::get('/app/token', 'AppController@token')->name('app.token');

        // Auth
        Route::post('/logout', 'AuthController@logout')->name('logout');

        // User
        Route::get('/account', 'AccountController@show')->name('account.show');
        Route::post('/account', 'AccountController@update')->name('account.update');
        Route::get('/account/notifications/{method?}', 'AccountController@notifications')->name('account.notifications.index');

        // Teams
        Route::get($plural, 'Team\TeamController@index')->name('teams.index');
        Route::post($plural, 'Team\TeamController@store')->name('teams.store');

        // Group: Teams
        Route::group(['prefix' => $plural.'/{team}'], function () {

            // Teams
            Route::get('/', 'Team\TeamController@show')->name('teams.show');
            Route::put('/', 'Team\TeamController@update')->name('teams.update');
            Route::delete('/', 'Team\TeamController@destroy')->name('teams.destroy');
            Route::post('/change', 'Team\TeamController@change')->name('teams.change');
            Route::post('/restore', 'Team\TeamController@restore')->name('teams.restore');

            // Subscription
            Route::post('/subscription', 'Subscription\SubscriptionController@subscription')->name('subscription');

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
            Route::get('/members', 'Team\TeamMemberController@index')->name('teams.members.index');
            Route::get('/members/{member}', 'Team\TeamMemberController@show')->name('teams.members.show');
            Route::put('/members/{member}', 'Team\TeamMemberController@update')->name('teams.members.update');
            Route::delete('/members/{member}', 'Team\TeamMemberController@destroy')->name('teams.members.destroy');
        });

        // Group: Administrator
        Route::group(['middleware' => 'administrator'], function () {

            // Dashboard
            Route::get('/dashboard', 'Dashboard\DashboardController@index')->name('dashboard.index');

            // Announcements
            Route::post('/announcements', 'AnnouncementController@store')->name('announcements.store');
            Route::put('/announcements/{announcement}', 'AnnouncementController@update')->name('announcements.update');
            Route::delete('/announcements/{announcement}', 'AnnouncementController@destroy')->name('announcements.destroy');

            // Users
            Route::get('/dashboard/users', 'Dashboard\DashboardController@users')->name('dashboard.users.index');
            Route::get('/dashboard/users/{user}', 'Dashboard\DashboardController@user')->name('dashboard.users.show');
            Route::post('/dashboard/users/search', 'Dashboard\DashboardController@searchUsers')->name('dashboard.users.search');
            Route::post('/dashboard/users/{user}/suspend', 'Dashboard\DashboardController@suspendUser')->name('dashboard.users.suspend');
            Route::post('/dashboard/users/{user}/unsuspend', 'Dashboard\DashboardController@unsuspendUser')->name('dashboard.users.unsuspend');

            // Impersonate
            Route::post('/dashboard/users/impersonate/{user}', 'Dashboard\ImpersonationController@store')->name('dashboard.users.impersonation.store');
            Route::delete('/dashboard/users/impersonate', 'Dashboard\ImpersonationController@destroy')->name('dashboard.users.impersonation.destroy');

            // Teams
            Route::get('/dashboard/teams', 'Dashboard\DashboardController@teams')->name('dashboard.teams.index');
            Route::get('/dashboard/teams/{team}', 'Dashboard\DashboardController@team')->name('dashboard.teams.show');
            Route::post('/dashboard/teams/search', 'Dashboard\DashboardController@searchTeams')->name('dashboard.teams.search');
            Route::post('/dashboard/teams/{team}/suspend', 'Dashboard\DashboardController@suspendTeam')->name('dashboard.teams.suspend');
            Route::post('/dashboard/teams/{team}/unsuspend', 'Dashboard\DashboardController@unsuspendTeam')->name('dashboard.teams.unsuspend');
        });
    });
});
