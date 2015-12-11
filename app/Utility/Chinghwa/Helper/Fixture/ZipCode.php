<?php

namespace App\Utility\Chinghwa\Helper\Fixture;

class ZipCode
{
	/**
     * Return zipcode in array|stdclass
     * 
     * @param  boolean $isArr
     * @return array|stdClass     
     */
    public static function get($isArr = true)
    {
    	return json_decode(self::getJsonSrc(), $isArr);
    }

	protected static function getJsonSrc()
    {
        return file_get_contents(__DIR__ . '/../../../../../storage/json/zipcode.json');
    }
}