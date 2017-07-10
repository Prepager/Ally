<?php

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
        Route::post('/login', 'AuthController@login')->name('login');
        Route::post('/login/refresh', 'AuthController@refresh')->name('refresh');
        Route::post('/register', 'AuthController@register')->name('register');
    });

    // Group: Authenticated
    Route::group(['middleware' => 'auth:api'], function () {

        // Variables
        $plural = str_plural(TeamPay::$teamName);

        // App
        Route::get('/app/token', 'AppController@token')->name('app.token');

        // Auth
        Route::get('/user', 'AuthController@user')->name('user');
        Route::post('/logout', 'AuthController@logout')->name('logout');
        Route::get('/notifications/{method?}', 'AuthController@notifications')->name('notifications');

        // Teams
        Route::get($plural, 'TeamController@index')->name('teams.index');
        Route::post($plural, 'TeamController@store')->name('teams.store');

        // Group: Teams
        Route::group(['prefix' => $plural.'/{team}'], function () {

            // Teams
            Route::get('/', 'TeamController@show')->name('teams.show');
            Route::put('/', 'TeamController@update')->name('teams.update');
            Route::delete('/', 'TeamController@destroy')->name('teams.destroy');
            Route::post('/change', 'TeamController@change')->name('teams.change');
            Route::post('/restore', 'TeamController@restore')->name('teams.restore');

            // Subscription
            Route::post('/subscription', 'SubscriptionController@subscription')->name('subscription');

            // Group: Subscribed
            Route::group(['middleware' => 'subscribed'], function () {

                // Subscription
                Route::post('/subscription/cancel', 'SubscriptionController@cancel')->name('subscription.cancel');
                Route::post('/subscription/resume', 'SubscriptionController@resume')->name('subscription.resume');
            });

            // Invoices
            Route::get('/invoices', 'SubscriptionController@invoices')->name('invoices');
            Route::get('/invoices/{id}', 'SubscriptionController@invoice')->name('invoice');

            // Members
            Route::get('/members', 'TeamMemberController@index')->name('teams.members.index');
            Route::get('/members/{member}', 'TeamMemberController@show')->name('teams.members.show');
            Route::put('/members/{member}', 'TeamMemberController@update')->name('teams.members.update');
            Route::delete('/members/{member}', 'TeamMemberController@destroy')->name('teams.members.destroy');
        });

        // Group: Administrator
        Route::group(['middleware' => 'administrator'], function () {

            // Dashboard
            Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

            // Users
            Route::get('/dashboard/users', 'DashboardController@users')->name('dashboard.users');
            Route::get('/dashboard/users/{user}'.'DashboardController@user')->name('dashboard.users.show');
            Route::post('/dashboard/users/search', 'DashboardController@searchUsers')->name('dashboard.users.search');

            // Impersonate
            Route::post('/dashboard/users/impersonate/{user}', 'DashboardController@impersonate')->name('dashboard.users.impersonate');
            Route::delete('/dashboard/users/impersonate', 'DashboardController@stopImpersonation')->name('dashboard.users.impersonate.stop');

            // Teams
            Route::get('/dashboard/teams', 'DashboardController@teams')->name('dashboard.teams');
            Route::get('/dashboard/teams/{team}'.'DashboardController@team')->name('dashboard.teams.show');
            Route::post('/dashboard/teams/search', 'DashboardController@searchTeams')->name('dashboard.teams.search');
        });
    });
});
