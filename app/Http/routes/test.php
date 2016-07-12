<?php

Route::group(['namespace' => 'Test', 'prefix' => 'test'], function() {
    Route::group(['middleware' => 'auth'], function() {
        Route::resource('tran_zipcode', 'TranZipcodeController');
        Route::get('/', ['uses' => 'TestController@backmail']);
    });    

    Route::any('/testwatcher', ['uses' => 'TestController@testwatcher']);
    Route::get('/exportfile', ['uses' => 'TestController@exportfile']);
    Route::get('/iron', ['uses' => 'TestController@iron']);
    Route::get('/slack', ['uses' => 'TestController@slack']);
    Route::get('/redis', ['uses' => 'TestController@redis']);
    Route::get('/mail', ['uses' => 'TestController@mail']);
});