<?php

// Group: API
Route::group(['middleware' => 'api'], function () {

    // Passport
    \Laravel\Passport\Passport::routes();

    // Cashier
    Route::post('/braintree/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');

    // Group: Namespaced
    Route::group(['namespace' => 'ZapsterStudios\TeamPay\Controllers'], function () {

        // Group: Unauthenticated
        Route::group([], function () {

            // Auth
            Route::post('/login', 'AuthController@login')->name('login');
            Route::post('/login/refresh', 'AuthController@refresh')->name('refresh');
            Route::post('/register', 'AuthController@register')->name('register');
        });

        // Group: Authenticated
        Route::group(['middleware' => 'auth:api'], function () {

            // Auth
            Route::get('/user', 'AuthController@user')->name('user');
            Route::post('/logout', 'AuthController@logout')->name('logout');
            Route::get('/notifications/{method?}', 'AuthController@notifications')->name('notifications');

            // Teams
            Route::apiResource('/'.str_plural(TeamPay::$teamName), 'TeamController', [
                'names' => [
                    'index' => 'teams.index',
                    'store' => 'teams.store',
                    'show' => 'teams.show',
                    'update' => 'teams.update',
                    'destroy' => 'teams.destroy',
                ]
            ]);

            // Group: Teams
            Route::group(['prefix' => '/'.str_plural(TeamPay::$teamName)], function () {

                // Members
                Route::apiResource('/{team}/members', 'TeamMemberController', [
                    'except' => ['store'],
                    'as' => 'team',
                ]);
            });
        });
    });
});
