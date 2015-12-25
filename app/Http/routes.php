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

Route::get('/event', ['uses' => 'Event\EventController@index']);

Route::get('/home', ['as' => 'index', function () {
    return view('base', ['title' => 'LubriNutrimate']);
}]);

Route::group(['middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '總經理辦公室']], function () {
	// 使用者
	Route::resource('user', 'User\UserController');
});

//Scrum
Route::group(['namespace' => 'Scrum', 'prefix' => 'scrum'], function () {
	Route::resource('todo', 'TodoController');
});

// POS 相關
Route::group(['namespace' => 'Pos', 'prefix' => 'pos/store'], function () {
	Route::resource('store', 'Store\StoreController');
	Route::resource('store_goal', 'Store\StoreGoalController');
	Route::resource('store_area', 'Store\StoreAreaController');
});

Route::group(['namespace' => 'Flap', 'prefix' => 'flap'], function () {
	Route::group(['middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '客戶經營一部', '客戶經營二部', '客戶經營三部', '客戶經營四部']], function () {
		Route::get('members', 'MemberController@index');
		Route::get('members/{code}', 'MemberController@show');
	});

	Route::group(['namespace' => 'CCS_OrderIndex', 'prefix' => 'ccs_order_index'], function () {
		Route::get('prefix/update', ['uses' => 'PrefixController@update', 'middleware' => 'report']);

		Route::get('cancelverify', ['uses' => 'CancelVerifyController@index', 'as' => 'ccs_orderindex_cancelverify_index']);
	});

	Route::group(['namespace' => 'CCS_ReturnGoodsI', 'prefix' => 'ccs_returngoodsi'], function () {
		Route::get('cancelverify', ['uses' => 'CancelVerifyController@index', 'as' => 'ccs_returngoodsi_cancelverify_index']);
	});

	Route::group(['namespace' => 'PIS_Goods', 'prefix' => 'pis_goods', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '供應部NEW']], function () {
		Route::get('fix_cprefix_goods', ['uses' => 'FixCPrefixGoodsController@index', 'as' => 'pis_goods_fix_cprefix_goods_index']);
		Route::put('fix_cprefix_goods', ['uses' => 'FixCPrefixGoodsController@update', 'as' => 'pis_goods_fix_cprefix_goods_update']);

		Route::get('copy_to_cometrust', ['uses' => 'CopyToCometrustController@index', 'as' => 'pis_goods_copy_to_cometrust_index']);
		Route::post('copy_to_cometrust', ['uses' => 'CopyToCometrustController@store', 'as' => 'pis_goods_copy_to_cometrust_store']);
	});
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

	// 每月初發送門市營業額分析-人報表
	Route::group(['prefix' => 'retail_sales_persontype'], function () {
		Route::get('', ['uses' => 'RetailSaleByPersonTypeController@index', 'as' => 'retail_sales_persontype_index']);
		Route::get('/process', ['uses' => 'RetailSaleByPersonTypeController@process', 'as' => 'retail_sales_persontype_process', 'middleware' => 'report']);
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

	// 客三成效追蹤
	// DirectSaleCorp3TraceController
	Route::group(['prefix' => 'directsale_corp3_trace'], function () {
		Route::get('/', ['uses' => 'DirectSaleCorp3TraceController@index', 'as' => 'directsale_corp3_trace_index']);
		Route::get('/process', ['uses' => 'DirectSaleCorp3TraceController@process', 'as' => 'directsale_corp3_trace_process', 'middleware' => 'report']);
	});
});

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

// 會員地址修正
// 設備檢查
Route::group(['namespace' => 'Fix', 'prefix' => 'fix'], function() {
	Route::get('/zipcode', ['uses' => 'ZipCodeController@index', 'as' => 'fix_zipcode']);
	Route::get('/birth', ['uses' => 'ZipCodeController@birth', 'as' => 'fix_birth']);
	Route::get('/equipment/ping', ['uses' => 'EquipmentController@ping', 'as' => 'fix_equipment_ping']);
	Route::get('/pis_goods/import', ['uses' => 'PISGoodsController@import', 'as' => 'pis_goods_import']);
});

