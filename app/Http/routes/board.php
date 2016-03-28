<?php

Route::group(['namespace' => 'Board', 'prefix' => 'board'], function () {
    Route::get('/marq', ['uses' => 'MarqController@index', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部']]);
});
