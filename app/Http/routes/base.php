<?php

Route::get('/', ['as' => 'index', function () {
    return view('base', ['title' => 'LubriNutrimate']);
}]);

Route::get('/is_alive', function () {
    //throw new \Exception('xxx');
    return 1;
});

Route::get('/home', ['as' => 'index', function () {
    return view('base', ['title' => 'LubriNutrimate']);
}]);

Route::get('/event', ['uses' => 'Event\EventController@index']);

Route::group(['middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '總經理辦公室']], function () {
    // 使用者
    Route::resource('user', 'User\UserController');
});

//Scrum
Route::group(['namespace' => 'Scrum', 'prefix' => 'scrum'], function () {
    Route::resource('todo', 'TodoController');
});

// 使用者資料匯入更新
Route::get('/user/feature/import', ['uses' => 'User\FeatureController@import', 'middleware' => 'report']);
Route::get('/user/feature/update', ['uses' => 'User\FeatureController@updateIpExt']);

// 文章發布
Route::resource('articles', 'ArticlesController');

// 介紹
Route::group(['namespace' => 'Intro', 'prefix' => 'intro'], function() {
    Route::get('/report', ['uses' => 'IntroController@report', 'as' => 'intro_report']);
    Route::get('/b', ['uses' => 'IntroController@b', 'as' => 'intro_b']);
});