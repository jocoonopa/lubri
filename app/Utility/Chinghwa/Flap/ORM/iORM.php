<?php

namespace App\Utility\Chinghwa\Flap\ORM;

interface iORM
{
    public static function isExist(array $options);
    public static function first(array $options);
    public static function find(array $options);
}