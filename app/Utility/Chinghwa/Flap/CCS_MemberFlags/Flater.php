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

    public static function getInflateFlag($flagString)
    {        
        $container = [];

        foreach (explode(' ', $flagString) as $pairString) {
            if (false === strpos($pairString, ':')) {
                continue;
            }

            $pair = explode(':', $pairString);

            $container[self::genKey(array_get($pair, 0))] = array_get($pair, 1);
        }

        return $container;
    }

    public static function getFlagString($flags)
    {
        $str = '';

        if (NULL === $flags) {
            return '';
        }

        foreach ($flags as $key => $flag) {
            $str .= self::resoveKey($key) . ':' . $flag . ' ';
        }

        return substr($str, 0, -1);
    }
}