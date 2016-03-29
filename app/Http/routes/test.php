<?php

Route::group(['namespace' => 'Test', 'prefix' => 'test'], function() {
    Route::group(['middleware' => 'auth'], function() {
        Route::resource('tran_zipcode', 'TranZipcodeController');
    });    
});