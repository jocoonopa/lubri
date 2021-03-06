<?php

// 自動發送報表
Route::group(['namespace' => 'Report', 'prefix' => 'report'], function() {
    // 訂單刷卡每日成交名單
    // (這邊由於是第一個撰寫的發送程序，那時規範還沒成立，路徑命名規則請勿參照此程序)
    Route::get('/credit_card/mail', ['uses' => 'CreditCardController@mail', 'as' => 'report_credit_card_mail', 'middleware' => 'report']);
    Route::get('/credit_card/', ['uses' => 'CreditCardController@index', 'as' => 'report_credit_card']);

    // 訂單補刷
    Route::get('/upbrush/process', ['uses' => 'CreditCardUpBrushController@process', 'as' => 'report_upbrush_mail', 'middleware' => 'report']);
    Route::get('/upbrush/', ['uses' => 'CreditCardUpBrushController@index', 'as' => 'report_upbrush']);

    // 門市營業額分析日報表
    Route::group(['prefix' => 'retail_sales'], function () {
        Route::get('/', ['uses' => 'RetailSalesController@index', 'as' => 'retail_sales_index']);
        Route::get('/process', ['uses' => 'RetailSalesController@process', 'as' => 'retail_sales_process', 'middleware' => 'report']);
        Route::get('/download', ['uses' => 'RetailSalesController@download', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '供應部NEW']]);
    });

    // 每周發送的員購銷貨單
    Route::group(['prefix' => 'emppurchase'], function () {
        Route::get('/', ['uses' => 'EmpPurchaseController@index', 'as' => 'emppurchase_index']);
        Route::get('/process', ['uses' => 'EmpPurchaseController@process', 'as' => 'emppurchase_process', 'middleware' => 'report']);
    });

    // 每月初發送門市營業額分析-人報表(公式)
    Route::group(['prefix' => 'retail_sales_person'], function () {
        Route::get('/process', ['uses' => 'RetailSalePersonController@process', 'as' => 'retail_sales_person_process', 'middleware' => 'report']);
    });

    // 每月初發送的康思特報表
    Route::group(['prefix' => 'conce'], function () {
        Route::get('/', ['uses' => 'ConceController@index', 'as' => 'conce_index']);
        Route::get('/process', ['uses' => 'ConceController@process', 'as' => 'conce_process', 'middleware' => 'report']);
    });

    // 每月初發送的進銷貨報表
    Route::group(['prefix' => 'spb'], function () {
        Route::get('/', ['uses' => 'SellAndPurchaseAndBackController@index', 'as' => 'spb_index']);
        Route::get('/process', ['uses' => 'SellAndPurchaseAndBackController@process', 'as' => 'spb_process', 'middleware' => 'report']);
    });

    // 每月初發送的促銷模組成效
    // PromoGradeController
    Route::group(['prefix' => 'promograde'], function () {
        Route::get('/', ['uses' => 'PromoGradeController@index', 'as' => 'promograde_index']);
        Route::get('/process', ['uses' => 'PromoGradeController@process', 'as' => 'promograde_process', 'middleware' => 'report']);
    });

    // 偉特 CTI Import Layout
    // CTILayoutController
    Route::group(['prefix' => 'ctilayout','middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '行銷處級辦公室','客戶經營三部主管']], function () {
        Route::get('/', ['uses' => 'CTILayoutController@index']);
        Route::get('/flap', ['uses' => 'CTILayoutController@flap']);
        Route::get('/cti', ['uses' => 'CTILayoutController@cti']);
        Route::get('/sync_list', ['uses' => 'CTILayoutController@syncList']);
        Route::get('/sync_member', ['uses' => 'CTILayoutController@syncMember']);
        Route::get('/campaign', ['uses' => 'CTILayoutController@campaign']);
    });

    // 每日業績
    // DailySaleRecordController
    Route::group(['prefix' => 'daily_sale_record'], function () {
        Route::get('/', ['uses' => 'DailySaleRecordController@index', 'as' => 'daily_sale_record_index']);
        Route::get('/process', ['uses' => 'DailySaleRecordController@process', 'as' => 'daily_sale_record_download', 'middleware' => 'report']);
        Route::get('/onlyec', ['uses' => 'DailySaleRecordController@onlyEc', 'middleware' => 'report']);
        Route::get('/onlycti', ['uses' => 'DailySaleRecordController@onlyCti', 'middleware' => 'report']);
    });

    // 每日回貨
    // BackGoodsController
    Route::group(['prefix' => 'daily_back_goods'], function () {
        Route::get('/', ['uses' => 'BackGoodsController@index', 'as' => 'daily_back_goods_index']);
        Route::get('/process', ['uses' => 'BackGoodsController@process', 'as' => 'daily_back_goods_process']);
    });

    // 客三成效追蹤
    // DirectSaleCorp3TraceController
    Route::group(['prefix' => 'directsale_corp3_trace'], function () {
        Route::get('/', ['uses' => 'DirectSaleCorp3TraceController@index', 'as' => 'directsale_corp3_trace_index']);
        Route::get('/process', ['uses' => 'DirectSaleCorp3TraceController@process', 'as' => 'directsale_corp3_trace_process', 'middleware' => 'report']);
    });

    // 每日業績商品版
    // DSRProductController
    Route::group(['prefix' => 'dsr_product'], function () {
        Route::get('/', ['uses' => 'DSRProductController@index']);
    });
});