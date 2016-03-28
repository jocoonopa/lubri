<?php

Route::group(['namespace' => 'Test', 'prefix' => 'test'], function() {
    Route::get('/toyihua', ['uses' => 'TestController@toYiHua', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部']]);
});