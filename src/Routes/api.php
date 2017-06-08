<?php

Route::group(['prefix' => 'api', 'middleware' => 'api'], function () {

    // Cashier
    Route::post('braintree/webhook', '\Laravel\Cashier\Http\Controllers\WebhookController@handleWebhook');

    // Auth
    Route::get('/test', function () {
        return 'dead';
    });
});
