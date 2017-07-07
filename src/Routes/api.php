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

    // Group: App
    Route::group(['prefix' => '/app'], function () {

        // Settings
        Route::get('/', 'AppController@index')->name('app');
        Route::get('/token', 'AppController@token')->name('app.token');
    });

    // Group: Unauthenticated
    Route::group([], function () {

        // Auth
        Route::post('/login', 'AuthController@login')->name('login');
        Route::post('/login/refresh', 'AuthController@refresh')->name('refresh');
        Route::post('/register', 'AuthController@register')->name('register');
    });

    // Group: Authenticated
    Route::group(['middleware' => 'auth:api'], function () {

        // Variables
        $plural = str_plural(TeamPay::$teamName);

        // Auth
        Route::get('/user', 'AuthController@user')->name('user');
        Route::post('/logout', 'AuthController@logout')->name('logout');
        Route::get('/notifications/{method?}', 'AuthController@notifications')->name('notifications');

        // Teams
        Route::get($plural, 'TeamController@index')->name('teams.index');
        Route::post($plural, 'TeamController@store')->name('teams.store');

        // Group: Teams
        Route::group(['prefix' => $plural.'/{team}'], function () {

            // Team
            Route::post('/change', 'TeamController@change')->name('teams.change');

            // Teams
            Route::get('/', 'TeamController@show')->name('teams.show');
            Route::put('/', 'TeamController@update')->name('teams.update');
            Route::delete('/', 'TeamController@destroy')->name('teams.destroy');

            // Subscription
            Route::post('/subscription', 'SubscriptionController@subscription')->name('subscription');

            // Group: Subscribed
            Route::group(['middleware' => 'subscribed'], function () {

                // Subscription
                Route::post('/subscription/cancel', 'SubscriptionController@cancel')->name('subscription.cancel');
                Route::post('/subscription/resume', 'SubscriptionController@resume')->name('subscription.resume');
            });

            // Members
            Route::get('/members', 'TeamMemberController@index')->name('teams.members.index');
            Route::get('/members/{member}', 'TeamMemberController@show')->name('teams.members.show');
            Route::put('/members/{member}', 'TeamMemberController@update')->name('teams.members.update');
            Route::delete('/members/{member}', 'TeamMemberController@destroy')->name('teams.members.destroy');
        });
    });
});
