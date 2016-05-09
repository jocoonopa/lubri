<?php

Route::group(['namespace' => 'CCS_OrderDivIndex', 'prefix' => 'ccs_order_div_index', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '供應部NEW']], function () {
    Route::get('/', ['uses' => 'FindDivController@index']);
});