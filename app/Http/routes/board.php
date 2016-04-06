<?php

Route::group(['namespace' => 'Board', 'prefix' => 'board'], function () {
    Route::get('/marq', ['uses' => 'MarqController@index', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '廣播']]);
    Route::get('/marq/group', ['uses' => 'MarqController@group', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '廣播']]);    
    Route::get('/marq/angular', ['uses' => 'MarqController@rivet', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '廣播']]);
});
