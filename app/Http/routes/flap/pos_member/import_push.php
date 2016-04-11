<?php

Route::group(['prefix' => 'import_push'], function () {
    Route::get('/{import_task}', ['uses' => 'ImportPushController@push', 'middleware' => ['import.push']])
    ->where(['import_task' => '[0-9]+']);

    Route::get('/{import_task}/content/{import_content}', ['uses' => 'ImportPushController@pushone', 'middleware' => ['import.push']])
    ->where(['import_task' => '[0-9]+', 'import_content' => '[0-9]+']);

    Route::get('/rollback', ['uses' => 'ImportPushController@rollback']);
    Route::get('/pull/{import_task}', ['uses' => 'ImportPushController@pull', 'middleware' => ['import.push']])
    ->where(['import_task' => '[0-9]+']);
});