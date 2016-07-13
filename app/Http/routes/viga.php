<?php

Route::group(['namespace' => 'Viga', 'prefix' => 'viga'], function () {
    Route::get('/que', ['uses' => 'QueController@index', 'middleware' => ['auth']]);
});
