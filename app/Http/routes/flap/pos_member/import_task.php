<?php

Route::group(['prefix' => 'import_task'], function () {
    Route::get('{import_task}/content', ['uses' => 'ImportContentController@index'])
    ->where(['import_content' => '[0-9]+']);
    
    Route::get('{import_task}/content/{import_content}', ['uses' => 'ImportContentController@show'])
    ->where(['import_content' => '[0-9]+']);

    Route::get('{import_task}/content/{import_content}/edit', ['uses' => 'ImportContentController@edit'])
    ->where(['import_content' => '[0-9]+']);

    Route::get('{import_task}/content/create', ['uses' => 'ImportContentController@create'])
    ->where(['import_content' => '[0-9]+']);

    Route::delete('{import_task}/content/{import_content}', ['uses' => 'ImportContentController@destroy', 'middleware' => ['import.content']])->where(['import_content' => '[0-9]+']);
    
    Route::post('{import_task}/content', ['uses' => 'ImportContentController@store'])
    ->where(['import_content' => '[0-9]+']);
    
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
Route::resource('import_kind', 'ImportKindController');