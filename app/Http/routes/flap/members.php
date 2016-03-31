<?php

Route::group(['prefix' => 'members', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '客戶經營一部', '客戶經營二部', '客戶經營三部', '客戶經營四部']], function () {
    Route::get('/', 'MemberController@index');
    Route::get('/{code}', 'MemberController@show');
});