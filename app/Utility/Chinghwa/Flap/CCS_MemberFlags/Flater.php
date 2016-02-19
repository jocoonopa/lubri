<?php

namespace App\Utility\Chinghwa\Flap\CCS_MemberFlags;

class Flater
{
    public static function genKey($val)
    {
        return "_{$val}_";
    }

    public static function resoveKey($val)
    {
        return str_replace('_', '', $val);
    }
}