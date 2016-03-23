<?php

Route::group(['namespace' => 'Flap', 'prefix' => 'flap'], function () {
    Route::get('db', 'DBController@find');
    Route::get('/db/testsp', 'DBController@testsp');

    Route::get('/manager', ['uses' => 'ManagerController@index', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部']]);

    Route::group(['middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '客戶經營一部', '客戶經營二部', '客戶經營三部', '客戶經營四部']], function () {
        Route::get('members', 'MemberController@index');
        Route::get('members/{code}', 'MemberController@show');
    });

    Route::group(['namespace' => 'CCS_OrderIndex', 'prefix' => 'ccs_order_index'], function () {
        Route::get('prefix/update', ['uses' => 'PrefixController@update', 'middleware' => 'report']);

        Route::get('cancelverify', ['uses' => 'CancelVerifyController@index', 'as' => 'ccs_orderindex_cancelverify_index']);

        Route::get('salerecord',  ['uses' => 'SaleRecordController@process', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部']]);

        Route::group(['prefix' => 'promote_shipment', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部','供應部NEW']], function () {
            Route::get('',  ['uses' => 'PromoteShipmentController@index']);
            Route::post('',  ['uses' => 'PromoteShipmentController@export']);
        });
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

    Route::group(['namespace' => 'POS_Member', 'prefix' => 'pos_member', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '行銷處級辦公室']], function () {
        Route::group(['prefix' => 'import_task'], function () {
            Route::get('{import_task}/content', ['uses' => 'ImportContentController@index'])->where(['import_content' => '[0-9]+']);
            Route::get('{import_task}/content/{import_content}', ['uses' => 'ImportContentController@show'])->where(['import_content' => '[0-9]+']);
            Route::get('{import_task}/content/{import_content}/edit', ['uses' => 'ImportContentController@edit'])->where(['import_content' => '[0-9]+']);
            Route::get('{import_task}/content/create', ['uses' => 'ImportContentController@create'])->where(['import_content' => '[0-9]+']);

            Route::delete('{import_task}/content/{import_content}', ['uses' => 'ImportContentController@destroy', 'middleware' => ['import.content']])->where(['import_content' => '[0-9]+']);
            Route::post('{import_task}/content', ['uses' => 'ImportContentController@store'])->where(['import_content' => '[0-9]+']);
            Route::put('{import_task}/content/{import_content}', ['uses' => 'ImportContentController@update', 'middleware' => ['import.content']]);
            Route::get('{import_task}/push_progress', ['uses' => 'ImportTaskController@pushProgress'])->where(['import_task' => '[0-9]+']);
            Route::get('{import_task}/pull_progress', ['uses' => 'ImportTaskController@pullProgress'])->where(['import_task' => '[0-9]+']);
            Route::get('{import_task}/export', ['uses' => 'ImportTaskController@export'])->where(['import_task' => '[0-9]+']);          

            /**
             * 必須擺在最後面, 否則會發生路徑衝突問題
             */
            Route::get('import_progesss', ['uses' => 'ImportTaskController@importProgress']);
        });

        Route::resource('import_task', 'ImportTaskController');

        Route::get('import_push/{import_task}', ['uses' => 'ImportPushController@push', 'middleware' => ['import.push']])
        ->where(['import_task' => '[0-9]+']);

        Route::get('import_push/{import_task}/content/{import_content}', ['uses' => 'ImportPushController@pushone', 'middleware' => ['import.push']])
        ->where(['import_task' => '[0-9]+', 'import_content' => '[0-9]+']);

        Route::get('import_push/rollback', ['uses' => 'ImportPushController@rollback']);
        Route::get('import_push/pull/{import_task}', ['uses' => 'ImportPushController@pull', 'middleware' => ['import.push']])
        ->where(['import_task' => '[0-9]+']);
    });
});