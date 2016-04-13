<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler;

interface IModelFactory
{
    /**
     * The model method will be override in deifferent way between different import way
     * 
     * @param  ImportColumnAdapter $adapter 
     */
    public function create($adapter);
}