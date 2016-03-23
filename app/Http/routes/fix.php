<?php

// 會員地址修正
// 設備檢查
Route::group(['namespace' => 'Fix', 'prefix' => 'fix'], function() {
    Route::get('/zipcode', ['uses' => 'ZipCodeController@index', 'as' => 'fix_zipcode']);
    Route::get('/birth', ['uses' => 'ZipCodeController@birth', 'as' => 'fix_birth']);
    Route::get('/equipment/ping', ['uses' => 'EquipmentController@ping', 'as' => 'fix_equipment_ping']);
    Route::get('/pis_goods/import', ['uses' => 'PISGoodsController@import', 'as' => 'pis_goods_import']);
});