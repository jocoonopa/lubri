<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['as' => 'index', function () {
    return view('base', ['title' => 'LubriNutrimate']);
}]);

// 文章發布
Route::resource('articles', 'ArticlesController');


// 介紹
Route::group(['namespace' => 'Intro', 'prefix' => 'intro'], function() {
	Route::get('/report', ['uses' => 'IntroController@report', 'as' => 'intro_report']);
	Route::get('/b', ['uses' => 'IntroController@b', 'as' => 'intro_b']);
});

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
		Route::get('/process', ['uses' => 'RetailSalesController@process', 'as' => 'retail_sales_process']);
	});

	// 每周發送的員購銷貨單
	Route::group(['prefix' => 'emppurchase'], function () {
		Route::get('/', ['uses' => 'EmpPurchaseController@index', 'as' => 'emppurchase_index']);
		Route::get('/process', ['uses' => 'EmpPurchaseController@process', 'as' => 'emppurchase_process']);
	});

	// 每月初發送的康思特報表
	Route::group(['prefix' => 'conce'], function () {
		Route::get('/', ['uses' => 'ConceController@index', 'as' => 'conce_index']);
		Route::get('/process', ['uses' => 'ConceController@process', 'as' => 'conce_process']);
	});

	// 每月初發送的進銷貨報表
	Route::group(['prefix' => 'spb'], function () {
		Route::get('/', ['uses' => 'SellAndPurchaseAndBackController@index', 'as' => 'spb_index']);
		Route::get('/process', ['uses' => 'SellAndPurchaseAndBackController@process', 'as' => 'spb_process']);
	});

	// 每月初發送的促銷模組成效
	// PromoGradeController
	Route::group(['prefix' => 'promograde'], function () {
		Route::get('/', ['uses' => 'PromoGradeController@index', 'as' => 'promograde_index']);
		Route::get('/process', ['uses' => 'PromoGradeController@process', 'as' => 'promograde_process']);
	});

	// 偉特 CTI Import Layout
	// CTILayoutController
	Route::group(['prefix' => 'ctilayout'], function () {
		Route::get('/', ['uses' => 'CTILayoutController@index', 'as' => 'ctilayout_index']);
		Route::get('/download', ['uses' => 'CTILayoutController@download', 'as' => 'ctilayout_download']);
	});

	// 每日業績
	// DailySaleRecordController
	Route::group(['prefix' => 'daily_sale_record'], function () {
		Route::get('/', ['uses' => 'DailySaleRecordController@index', 'as' => 'daily_sale_record_index']);
		Route::get('/process', ['uses' => 'DailySaleRecordController@process', 'as' => 'daily_sale_record_download']);
	});
});

// 會員比對類的處理都在這裡
Route::group(['namespace' => 'Compare', 'prefix' => 'compare'], function() {
	Route::get('/m64', ['uses' => 'Magazine64Controller@index', 'as' => 'compare_m64']);
	Route::post('/m64', 'Magazine64Controller@process');

	Route::get('/honeybaby', ['uses' => 'HoneyBabyController@index', 'as' => 'compare_honeybaby']);
	Route::post('/honeybaby', 'HoneyBabyController@process');

	Route::get('/honeybaby/download/insert', ['uses' => 'HoneyBabyController@downloadInsert', 'as' => 'compare_honeybaby_download_insert']);
	Route::get('/honeybaby/download/update', ['uses' => 'HoneyBabyController@downloadUpdate', 'as' => 'compare_honeybaby_download_update']);
});

Route::group(['namespace' => 'Broad', 'prefix' => 'broad'], function() {
	Route::get('/todo', ['uses' => 'TodoController@index', 'as' => 'broad_todo']);
});

// 會員地址修正
Route::group(['namespace' => 'Fix', 'prefix' => 'fix'], function() {
	Route::get('/zipcode', ['uses' => 'ZipCodeController@index', 'as' => 'fix_zipcode']);
	Route::get('/birth', ['uses' => 'ZipCodeController@birth', 'as' => 'fix_birth']);
});

