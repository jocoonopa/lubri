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