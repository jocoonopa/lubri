<?php

Route::group(['namespace' => 'PIS_Goods', 'prefix' => 'pis_goods', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '供應部NEW']], function () {
    Route::get('fix_cprefix_goods', ['uses' => 'FixCPrefixGoodsController@index', 'as' => 'pis_goods_fix_cprefix_goods_index']);
    Route::put('fix_cprefix_goods', ['uses' => 'FixCPrefixGoodsController@update', 'as' => 'pis_goods_fix_cprefix_goods_update']);

    Route::get('copy_to_cometrust', ['uses' => 'CopyToCometrustController@index', 'as' => 'pis_goods_copy_to_cometrust_index']);
    Route::post('copy_to_cometrust', ['uses' => 'CopyToCometrustController@store', 'as' => 'pis_goods_copy_to_cometrust_store']);
});