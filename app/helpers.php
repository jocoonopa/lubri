<?php

function c8($str)
{
    return mb_convert_encoding($str, 'UTF-8', 'big5');
}

function cb5($str)
{
    return mb_convert_encoding($str, 'big5', 'UTF-8');
}

function srp($str, $placeholder = [])
{
    return str_replace(getReplaceWords(), $placeholder, $str);
}

function getReplaceWords()
{
    return ['-', '(', ')', '　', '\''];
}

function getArrayVal($arr, $key, $default = '')
{
    return array_key_exists($key, $arr) ? $arr[$key] : $default;
}

function getRowVal($row, $key, $default = '')
{
    return isset($row[$key]) ? $row[$key] : $default;
}

function pr($v)
{
    echo "<pre>"; print_r($v); echo "</pre>";
}

function c8res(&$row)
{
    $tmp = [];

    foreach ($row as $key => $value) {
        $tmp[c8($key)] = c8($value);
    }

    $row = $tmp;
}

function cb5res(&$row)
{
    $tmp = [];

    foreach ($row as $key => $value) {
        $row[cb5($key)] = cb5($value);
    }

    $row = $tmp;
}

function arraySelect(array $arr, array $targets, $column)
{
    $indexList = [];

    foreach ($arr as $key => $ele) {
        if (in_array($ele[$column], $targets)) {
            $indexList[] = $key;
        }
    }

    return $indexList;
}

function str_replace_first($search, $replace, $subject) {
    $pos = strpos($subject, $search);
    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }
    return $subject;
}

function tranSex($num)
{
    switch ($num) {
        case 0:
        case 2:
            return '小姐';

        case 1:
            return '先生';

        default:
            return '';
    }
}

function tranAge($dateString)
{
    if ('' === $dateString) {
        return 0;
    }

    $shift = substr($dateString, 4, 4) > date('md') ? 1 : 0;

    return date('Y') - substr($dateString, 0, 4) + $shift;
}

function assignAgeGroup($dateString)
{
    $age = tranAge($dateString);

    if ($age < 20) {
        return 'YG-1';
    } elseif ($age < 35) {
        return 'YG-2';
    } elseif ($age < 50) {
        return 'YG-3';
    } elseif ($age < 65) {
        return 'YG-4';
    } elseif ($age < 80) {
        return 'YG-5';
    } else {
        return 'YG-6';
    }
}

function array_declare(array $a, $assign = NULL)
{
    $tmp = [];

    foreach ($a as $v) {
        $tmp[$v] = $assign;
    }

    return $tmp;
}