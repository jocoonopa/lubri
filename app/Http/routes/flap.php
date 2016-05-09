<?php

Route::group(['namespace' => 'Flap', 'prefix' => 'flap'], function () {    
    Route::get('/manager', ['uses' => 'ManagerController@index', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部']]);

    require_once 'flap/db.php';
    require_once 'flap/members.php';
    require_once 'flap/ccs_order_index.php';
    require_once 'flap/ccs_order_div_index.php';
    require_once 'flap/ccs_returngoodsi.php';
    require_once 'flap/pis_goods.php';
    require_once 'flap/pos_member.php';
});