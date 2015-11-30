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

Route::get('/home', ['as' => 'index', function () {
    return view('base', ['title' => 'LubriNutrimate']);
}]);

// 使用者
Route::resource('user', 'User\UserController');

Route::group(['namespace' => 'Flap', 'prefix' => 'flap'], function () {
	Route::get('members', 'MemberController@index');
	Route::get('members/{code}', 'MemberController@show');
});

// 使用者資料匯入更新
Route::get('/user/feature/import', ['uses' => 'User\FeatureController@import', 'middleware' => 'report']);
Route::get('/user/feature/update', ['uses' => 'User\FeatureController@updateIpExt']);

// 文章發布
Route::resource('articles', 'ArticlesController');

// 權限相關路徑
Route::group(['namespace' => 'Auth', 'prefix' => 'auth'], function () {
	Route::get('login', 'AuthController@getLogin');
	Route::post('login', 'AuthController@postLogin');
	Route::get('logout', 'AuthController@getLogout');

	// // Registration routes...
	// Route::get('register', 'AuthController@getRegister');
	// Route::post('register', 'AuthController@postRegister');
	 
	// Password reset link request routes...
	Route::get('password/email', 'PasswordController@getEmail');
	Route::post('password/email', 'PasswordController@postEmail');

	// Password reset routes...
	Route::get('password/reset/{token}', 'PasswordController@getReset');
	Route::post('password/reset', 'PasswordController@postReset');
});

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
		Route::get('/process', ['uses' => 'RetailSalesController@process', 'as' => 'retail_sales_process', 'middleware' => 'report']);
	});

	// 每周發送的員購銷貨單
	Route::group(['prefix' => 'emppurchase'], function () {
		Route::get('/', ['uses' => 'EmpPurchaseController@index', 'as' => 'emppurchase_index']);
		Route::get('/process', ['uses' => 'EmpPurchaseController@process', 'as' => 'emppurchase_process', 'middleware' => 'report']);
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

	// 每日回貨
	// BackGoodsController
	Route::group(['prefix' => 'daily_back_goods'], function () {
		Route::get('/', ['uses' => 'BackGoodsController@index', 'as' => 'daily_back_goods_index']);
		Route::get('/process', ['uses' => 'BackGoodsController@process', 'as' => 'daily_back_goods_process']);
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
	Route::get('/honeybaby/download/insert_example', ['uses' => 'HoneyBabyController@downloadInsertExample', 'as' => 'compare_honeybaby_download_insert_example']);
	Route::get('/honeybaby/download/update_example', ['uses' => 'HoneyBabyController@downloadUpdateExample', 'as' => 'compare_honeybaby_download_update_example']);

	Route::group(['prefix' => 'financial_strike_balance'], function () {
		Route::get('/', ['uses' => 'FinancialStrikeBalanceController@index', 'as' => 'compare_financial_strike_balance_index']);
		Route::any('/donsun', ['uses' => 'FinancialStrikeBalanceController@donsun', 'as' => 'compare_financial_strike_balance_donsun']);
	});
});

// 會員地址修正
// 設備檢查
Route::group(['namespace' => 'Fix', 'prefix' => 'fix'], function() {
	Route::get('/zipcode', ['uses' => 'ZipCodeController@index', 'as' => 'fix_zipcode']);
	Route::get('/birth', ['uses' => 'ZipCodeController@birth', 'as' => 'fix_birth']);
	Route::get('/equipment/ping', ['uses' => 'EquipmentController@ping', 'as' => 'fix_equipment_ping']);
	Route::get('/distflag', ['uses' => 'DistFlagController@index', 'as' => 'fix_distflag']);
	Route::get('/pis_goods/import', ['uses' => 'PISGoodsController@import', 'as' => 'pis_goods_import']);
});

