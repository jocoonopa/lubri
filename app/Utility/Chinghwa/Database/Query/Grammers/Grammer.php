<?php

namespace App\Utility\Chinghwa\Database\Query\Grammers;

class Grammer
{
	public static function genInQuery($data, $isWrap = false)
	{
	    $str = NULL;

	    foreach ($data as $val) {
	    	$val = str_replace("'", '', $val);
	    	
	        $str .= "'{$val}',";
	    }

	    return (!$isWrap) ? substr($str, 0, -1) : ' (' . substr($str, 0, -1) . ')';
	}

	public static function genQueryNestReplace(array $targetArr, array $replaceArr, $columnName)
	{
	    $string = '';

	    foreach ($targetArr as $key => $target) {
	        $target = ('\'' === $target) ? $target . '\'' : $target;
	        $columnName = (0 === $key) ? $columnName : $string; 

	        $string = 'REPLACE('. $columnName .', \'' . $target . '\', \'' . getArrayVal($replaceArr, $key) . '\')';
	    }

	    return $string;
	}
}