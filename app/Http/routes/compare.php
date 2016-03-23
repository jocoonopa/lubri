<?php

// 會員比對類的處理都在這裡
Route::group(['namespace' => 'Compare', 'prefix' => 'compare'], function() {
    Route::get('/honeybaby', ['uses' => 'HoneyBabyController@index', 'as' => 'compare_honeybaby']);
    Route::post('/honeybaby', ['uses' => 'HoneyBabyController@process', 'middleware' => 'honeybaby']);

    Route::get('/honeybaby/download/insert', ['uses' => 'HoneyBabyController@downloadInsert', 'as' => 'compare_honeybaby_download_insert']);
    Route::get('/honeybaby/download/update', ['uses' => 'HoneyBabyController@downloadUpdate', 'as' => 'compare_honeybaby_download_update']);
    Route::get('/honeybaby/download/insert_example', ['uses' => 'HoneyBabyController@downloadInsertExample', 'as' => 'compare_honeybaby_download_insert_example']);
    Route::get('/honeybaby/download/update_example', ['uses' => 'HoneyBabyController@downloadUpdateExample', 'as' => 'compare_honeybaby_download_update_example']);

    Route::group(['prefix' => 'financial_strike_balance'], function () {
        Route::get('/', ['uses' => 'FinancialStrikeBalanceController@index', 'as' => 'compare_financial_strike_balance_index']);
        Route::any('/donsun', ['uses' => 'FinancialStrikeBalanceController@donsun', 'as' => 'compare_financial_strike_balance_donsun']);
    });
});