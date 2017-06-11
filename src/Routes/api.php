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

        // Unauthenticated
        Route::group([], function () {

            // Auth
            Route::post('/login', 'AuthController@login');
            Route::post('/login/refresh', 'AuthController@refresh');
            Route::post('/register', 'AuthController@register');
        });

        // Authenticated
        Route::group(['middleware' => 'auth:api'], function () {

            // Auth
            Route::post('/logout', 'AuthController@logout');

            // Teams
            Route::apiResource('/'.str_plural(TeamPay::$teamName), 'TeamController');

        });
    });
});
