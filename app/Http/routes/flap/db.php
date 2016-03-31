<?php

Route::group(['prefix' => 'db'], function () {
    Route::get('/', 'DBController@find');
    Route::get('/testsp', 'DBController@testsp');
});