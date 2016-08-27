<?php

Route::group(['namespace' => 'Viga', 'prefix' => 'viga'], function () {
    Route::get('/que', ['uses' => 'QueController@index', 'middleware' => ['auth']]);
    Route::get('/que/{fvsyncque}', ['uses' => 'QueController@show', 'middleware' => ['auth']])->where(['fvsyncque' => '[0-9]+']);
});
