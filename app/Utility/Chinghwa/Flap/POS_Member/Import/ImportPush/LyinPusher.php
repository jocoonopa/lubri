<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportPush;

use App\Import\Flap\POS_Member\Import;
use App\Model\Flap\PosMemberImportContent;

class LyinPusher extends Pusher
{    
    protected function getInsertProcQuery(PosMemberImportContent $content)
    {
        $sql = "exec dbo.sp_InsertHBMember ";
        $sql.= "{$this->getWrapVal($content->serno)}";
        $sql.= ", {$this->getWrapVal($content->code)}";
        $sql.= ", {$this->getWrapVal($content->sernoi)}";
        $sql.= ",{$this->getWrapVal($content->name)},";
        $sql.= ("'" . Import::FEMALE_SEX_TEXT . "'" === $this->getWrapVal($content->sex)) ? '0' : '1';
        $sql.= ",{$this->getWrapVal($content->birthday)}";
        $sql.= ",{$this->getWrapVal($content->hometel)}";
        $sql.= ",{$this->getWrapVal($content->officetel)}";
        $sql.= ",{$this->getWrapVal($content->cellphone)}";
        $sql.= ",{$this->getWrapVal($content->email)}";
        $sql.= ",{$this->getWrapVal($content->getZipcode())}";
        $sql.= ",{$this->getWrapVal($content->getCityName())}";
        $sql.= ",{$this->getWrapVal($content->getStateName())}";
        $sql.= ",{$this->getWrapVal($content->homeaddress)}";
        $sql.= ",{$this->getWrapVal($content->category)}";
        $sql.= ",{$this->getWrapVal($content->salepoint_serno)}";
        $sql.= ",{$this->getWrapVal($content->employee_serno)}";
        $sql.= ",{$this->getWrapVal($content->exploit_serno)}";
        $sql.= ",{$this->getWrapVal($content->exploit_emp_serno)}";
        $sql.= ",{$this->getWrapVal($content->distinction)}";
        $sql.= ",{$this->getWrapVal($content->member_level_ec)}";
        $sql.= ",{$this->getWrapVal($content->employ_code)}";
        $sql.= ",{$this->getWrapVal($content->memo)}";

        return $sql;
    }

    protected function getUpdateProcQuery(PosMemberImportContent $content)
    {
        $memo = $this->getWrapVal($content->memo);
        
        return "UPDATE CCS_CRMFields SET CRMNote1={$memo} WHERE MemberSerNoStr = '{$content->serno}'";
    }
}