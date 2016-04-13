<?php

namespace App\Utility\Chinghwa\Flap\ORM;

use App\Model\Flap\PosMemberImportContent;
use App\Utility\Chinghwa\Flap\CCS_MemberFlags\Flater;

class CCS_MemberFlags implements iORM
{
    public static function genUpdateFlagQueryByContent(PosMemberImportContent $content)
    {
        $sql = 'UPDATE CCS_MemberFlags SET ';

        foreach ($content->flags as $key => $flag) {
            $key = Flater::resoveKey($key);

            $sql.= "Distflags_{$key}='{$flag}',";
        }

        return substr($sql, 0, -1) . " WHERE MemberSerNoStr='$content->serno'";
    }

    public static function isExist(array $options){}
    public static function first(array $options){}
    public static function find(array $options){}
}