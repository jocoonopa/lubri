<?php

namespace App\Utility\Chinghwa\Helper\Report;

class DailySaleRecordHelper
{
    /**
     * 'CH51000' 直效行銷處級辦公室
     * 'CH53000' 客經1
     * 'CH54000' 客經2
     * 'CH54100' 客經3
     * 'CH54200' 客經4
     * 
     * @return array
     */
    public static function getErpGroupList()
    {
        return [
            'CH51000', 
            'CH53000', 
            'CH54000', 
            'CH54100', 
            'CH54200' 
        ];
    }

    public static function getExcelHead()
    {
        return [
            '部門',
            '人員代碼',
            '姓名',      
            '會員數',     
            '訂單數',     
            '淨額',      
            '會員均單',    
            '訂單均價',    
            '撥打會員數',   
            '撥打通數',    
            '撥打秒數',
            '工作日' 
        ];
    }

    public static function getPosGroupList()
    {
        return [
            'S008',
            'S009',
            'S013',
            'S014',
            'S017',
            'S028',
            'S049',
            'S051'
        ];
    }
}