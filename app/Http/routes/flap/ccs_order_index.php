<?php

Route::group(['namespace' => 'CCS_OrderIndex', 'prefix' => 'ccs_order_index'], function () {
    Route::get('prefix/update', ['uses' => 'PrefixController@update', 'middleware' => 'report']);

    Route::get('cancelverify', ['uses' => 'CancelVerifyController@index', 'as' => 'ccs_orderindex_cancelverify_index']);

    Route::get('salerecord',  ['uses' => 'SaleRecordController@process', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部']]);

    Route::group(['prefix' => 'promote_shipment', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部','供應部NEW']], function () {
        Route::get('',  ['uses' => 'PromoteShipmentController@index']);
        Route::post('',  ['uses' => 'PromoteShipmentController@export']);
    });
});