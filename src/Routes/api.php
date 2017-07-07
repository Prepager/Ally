<?php

// Group: API
Route::group(['middleware' => 'api'], function () {

    // Passport
    \Laravel\Passport\Passport::routes();

    // Cashier
    Route::post('/braintree/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');

    // Group: Namespaced
    Route::group(['namespace' => 'ZapsterStudios\TeamPay\Controllers'], function () {

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
            Route::apiResource('/'.$plural, 'TeamController', [
                'names' => [
                    'index' => 'teams.index',
                    'store' => 'teams.store',
                    'show' => 'teams.show',
                    'update' => 'teams.update',
                    'destroy' => 'teams.destroy',
                ],
            ]);

            // Group: Teams
            Route::group(['prefix' => '/'.$plural.'/{team}'], function () {

                // Subscription
                Route::post('/subscription', 'SubscriptionController@subscription')->name('subscription');

                // Group: Subscribed
                Route::group(['middleware' => 'subscribed'], function () {

                    // Subscription
                    Route::post('/subscription/cancel', 'SubscriptionController@cancel')->name('subscription.cancel');
                    Route::post('/subscription/resume', 'SubscriptionController@resume')->name('subscription.resume');
                });

                // Members
                Route::apiResource('/members', 'TeamMemberController', [
                    'except' => ['store'],
                    'as' => 'team',
                ]);
            });
        });
    });
});
