<?php

namespace App\Http\Validator;

use App\Model\State;
use App\Utility\Chinghwa\Flap\POS_Member\Filter;
use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;

class ImportContentValidator
{
    protected $filter;

    public function __construct()
    {
        $this->filter = new Filter;
    }

    public function zipcode($attribute, $value, $parameters, $validator) 
    {
        $value = $this->filter->_getZipcode($value);

        return (Import::DEFAULT_ZIPCODE === $value || NULL !== State::findByZipcode($value)->first());
    }

    public function cellphone($attribute, $value, $parameters, $validator) 
    {
        return $this->filter->isCellphoneLengthValid($this->filter->genProperFormatCellphone($value));
    }

    public function tel($attribute, $value, $parameters, $validator) 
    {
        return $this->filter->isTelLengthValid($value);
    }
}