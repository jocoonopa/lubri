<?php

Route::group(['namespace' => 'POS_Member', 'prefix' => 'pos_member', 'middleware' => ['auth', 'auth.corp'], 'corp' => ['資訊部', '行銷處級辦公室']], function () {
    
    require_once 'pos_member/import_task.php';
    require_once 'pos_member/import_push.php';
    require_once 'pos_member/import_activity_task.php';    
    require_once 'pos_member/import_activity_push.php';
});