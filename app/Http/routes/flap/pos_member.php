<?php

Route::group(['namespace' => 'POS_Member', 'prefix' => 'pos_member', 'middleware' => ['auth']], function () {    
    require_once 'pos_member/import_task.php';
    require_once 'pos_member/import_push.php';
});