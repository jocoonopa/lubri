<?php

Route::group(['prefix' => 'import_activity_task'], function () {
    Route::get('{import_activity_task}/content', ['uses' => 'ImportActivityContentController@index'])->where(['import_content' => '[0-9]+']);
    Route::get('{import_activity_task}/content/{import_content}', ['uses' => 'ImportActivityContentController@show'])->where(['import_content' => '[0-9]+']);
    Route::get('{import_activity_task}/content/{import_content}/edit', ['uses' => 'ImportActivityContentController@edit'])->where(['import_content' => '[0-9]+']);
    Route::get('{import_activity_task}/content/create', ['uses' => 'ImportActivityContentController@create'])->where(['import_content' => '[0-9]+']);

    Route::delete('{import_activity_task}/content/{import_content}', ['uses' => 'ImportActivityContentController@destroy', 'middleware' => ['import.content']])->where(['import_content' => '[0-9]+']);
    Route::post('{import_activity_task}/content', ['uses' => 'ImportActivityContentController@store'])->where(['import_content' => '[0-9]+']);
    Route::put('{import_activity_task}/content/{import_content}', ['uses' => 'ImportActivityContentController@update', 'middleware' => ['import.content']]);
    Route::get('{import_activity_task}/push_progress', ['uses' => 'ImportActivityTaskController@pushProgress'])->where(['import_activity_task' => '[0-9]+']);
    Route::get('{import_activity_task}/pull_progress', ['uses' => 'ImportActivityTaskController@pullProgress'])->where(['import_activity_task' => '[0-9]+']);
    Route::get('{import_activity_task}/export', ['uses' => 'ImportActivityTaskController@export'])->where(['import_activity_task' => '[0-9]+']);          

    /**
     * 必須擺在最後面, 否則會發生路徑衝突問題
     */
    Route::get('import_progesss', ['uses' => 'ImportActivityTaskController@importProgress']);
});

Route::resource('import_activity_task', 'ImportActivityTaskController');