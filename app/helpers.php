<?php

if (!defined('STDIN')) {
    define('STDIN', fopen('php://stdin', 'r'));
}

function c8($str)
{
    return mb_convert_encoding($str, 'UTF-8', 'big5');
}

function cb5($str)
{
    return mb_convert_encoding($str, 'big5', 'UTF-8');
}

function csvStrFilter($str)
{
    return str_replace([',', "\n", "\r"], '', $str);
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

function keepOnlyNumber($number)
{
    return preg_replace_callback(
        '/\D/',
        function ($v) {
            $v = str_replace($v, '', $v);
            
            return $v[0];
        },
        $number
    );
}

function keepOnlyChineseWord($str)
{
    preg_match_all('/[\x{4e00}-\x{9fff}]+/u', $str, $matches);

    return strFilter(join('', $matches[0]));
}

function validateDate($date, $dateType = 'Y-m-d')
{
    $newDate = \DateTime::createFromFormat($dateType, $date);
    
    return $newDate && ($date === $newDate->format($dateType));
}

/**
 * 來源: http://help.i2yes.com/?q=node/236
 * 全形半形轉換
 *
 * @param  string $strs  
 * @param  mixed $types 1: 轉全形, 其他為轉半形
 * @return string
 */
function nfTowf($strs, $types = 0)
{  
    $nft = [
        "(", ")", "[", "]", "{", "}", ".", ",", ";", ":",
        "-", "?", "!", "@", "#", "$", "%", "&", "|", "\\",
        "/", "+", "=", "*", "~", "`", "'", "\"", "<", ">",
        "^", "_",
        "0", "1", "2", "3", "4", "5", "6", "7", "8", "9",
        "a", "b", "c", "d", "e", "f", "g", "h", "i", "j",
        "k", "l", "m", "n", "o", "p", "q", "r", "s", "t",
        "u", "v", "w", "x", "y", "z",
        "A", "B", "C", "D", "E", "F", "G", "H", "I", "J",
        "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T",
        "U", "V", "W", "X", "Y", "Z",
        " "
    ];

    $wft = [
        "（", "）", "〔", "〕", "｛", "｝", "﹒", "，", "；", "：",
        "－", "？", "！", "＠", "＃", "＄", "％", "＆", "｜", "＼",
        "／", "＋", "＝", "＊", "～", "、", "、", "＂", "＜", "＞",
        "︿", "＿",
        "０", "１", "２", "３", "４", "５", "６", "７", "８", "９",
        "ａ", "ｂ", "ｃ", "ｄ", "ｅ", "ｆ", "ｇ", "ｈ", "ｉ", "ｊ",
        "ｋ", "ｌ", "ｍ", "ｎ", "ｏ", "ｐ", "ｑ", "ｒ", "ｓ", "ｔ",
        "ｕ", "ｖ", "ｗ", "ｘ", "ｙ", "ｚ",
        "Ａ", "Ｂ", "Ｃ", "Ｄ", "Ｅ", "Ｆ", "Ｇ", "Ｈ", "Ｉ", "Ｊ",
        "Ｋ", "Ｌ", "Ｍ", "Ｎ", "Ｏ", "Ｐ", "Ｑ", "Ｒ", "Ｓ", "Ｔ",
        "Ｕ", "Ｖ", "Ｗ", "Ｘ", "Ｙ", "Ｚ",
        "　"
    ];
 
    return (1 === $types) ? str_replace($nft, $wft, $strs) : str_replace($wft, $nft, $strs);
}

function strFilter ($str) {
    $str = str_replace('`', '', $str);
    $str = str_replace('·', '', $str);
    $str = str_replace('~', '', $str);
    $str = str_replace('!', '', $str);
    $str = str_replace('！', '', $str);
    $str = str_replace('@', '', $str);
    $str = str_replace('#', '', $str);
    $str = str_replace('$', '', $str);
    $str = str_replace('￥', '', $str);
    $str = str_replace('%', '', $str);
    $str = str_replace('^', '', $str);
    $str = str_replace('……', '', $str);
    $str = str_replace('&', '', $str);
    $str = str_replace('*', '', $str);
    $str = str_replace('(', '', $str);
    $str = str_replace(')', '', $str);
    $str = str_replace('（', '', $str);
    $str = str_replace('）', '', $str);
    $str = str_replace('-', '', $str);
    $str = str_replace('_', '', $str);
    $str = str_replace('——', '', $str);
    $str = str_replace('+', '', $str);
    $str = str_replace('=', '', $str);
    $str = str_replace('|', '', $str);
    $str = str_replace('\\', '', $str);
    $str = str_replace('[', '', $str);
    $str = str_replace(']', '', $str);
    $str = str_replace('【', '', $str);
    $str = str_replace('】', '', $str);
    $str = str_replace('{', '', $str);
    $str = str_replace('}', '', $str);
    $str = str_replace(';', '', $str);
    $str = str_replace('；', '', $str);
    $str = str_replace(':', '', $str);
    $str = str_replace('：', '', $str);
    $str = str_replace('\'', '', $str);
    $str = str_replace('"', '', $str);
    $str = str_replace('「', '', $str);
    $str = str_replace('」', '', $str);
    $str = str_replace(',', '', $str);
    $str = str_replace('，', '', $str);
    $str = str_replace('<', '', $str);
    $str = str_replace('>', '', $str);
    $str = str_replace('《', '', $str);
    $str = str_replace('》', '', $str);
    $str = str_replace('.', '', $str);
    $str = str_replace('。', '', $str);
    $str = str_replace('/', '', $str);
    $str = str_replace('、', '', $str);
    $str = str_replace('?', '', $str);
    $str = str_replace('？', '', $str);
    
    return trim($str);
}

function isWrongCodeTel($content)
{
    return false === strpos($content->hometel, App\Utility\Chinghwa\Flap\POS_Member\Import\Import::WRONG_TELCODE);
}

function bomstr()
{
    return chr(239) . chr(187) . chr(191);
}

function sqlInWrap(array $strs) 
{
    $tmp = '';

    foreach ($strs as $str) {
        $tmp .= "'{$str}',";
    }

    return substr($tmp, 0, -1);
}

function getLastLineOfFile($file)
{
    $line = '';

    $f = fopen($file, 'r');
    $cursor = -1;

    fseek($f, $cursor, SEEK_END);
    $char = fgetc($f);

    /**
     * Trim trailing newline chars of the file
     */
    while ($char === "\n" || $char === "\r") {
        fseek($f, $cursor --, SEEK_END);
        $char = fgetc($f);
    }

    /**
     * Read until the start of file or first newline char
     */
    while ($char !== false && $char !== "\n" && $char !== "\r") {
        /**
         * Prepend the new char
         */
        $line = $char . $line;
        fseek($f, $cursor--, SEEK_END);
        $char = fgetc($f);
    }

    return $line;
}