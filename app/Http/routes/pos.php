<?php

Route::group(['namespace' => 'POS', 'prefix' => 'pos', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '供應部NEW']], function() {
    Route::resource('store_goal', 'StoreGoalController');
});