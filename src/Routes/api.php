<?php

Route::group(['prefix' => 'api', 'middleware' => 'api'], function () {
    
    // Auth
    Route::get('/test', function() {
        return 'dead';
    });
    
});