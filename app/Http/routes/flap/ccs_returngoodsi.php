<?php

Route::group(['namespace' => 'CCS_ReturnGoodsI', 'prefix' => 'ccs_returngoodsi'], function () {
    Route::get('cancelverify', ['uses' => 'CancelVerifyController@index', 'as' => 'ccs_returngoodsi_cancelverify_index']);
});