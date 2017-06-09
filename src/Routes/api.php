<?php

// Group
Route::group([
    'middleware' => 'api',
], function () {

    // Passport
    \Laravel\Passport\Passport::routes();

    // Cashier
    Route::post('/braintree/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');

    // Namespace
    Route::group(['namespace' => 'ZapsterStudios\TeamPay\Controllers'], function () {

        // Auth
        Route::post('/login', 'AuthController@login');
        Route::post('/login/refresh', 'AuthController@refresh');

        Route::get('/test', function () {
            return config('app.env');
        });

        // Authenticated
        Route::group(['middleware' => 'auth:api'], function () {

            // Auth
            Route::post('/logout', 'AuthController@logout');
        });
    });
});
