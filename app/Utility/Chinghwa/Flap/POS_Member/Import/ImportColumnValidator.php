<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

class ImportColumnValidator
{
	public function isValid($columns)
	{
        return ($this->isNameLengthEnough($columns) 
            && (
                $this->isAddressLengthEnough($columns) 
                || $this->isHometelLengthEnough($columns)
                || $this->isCellPhoneLengthEnough($columns)
                || $this->isEmailValid($columns)
            )
        );
	}

    protected function isNameLengthEnough($columns)
    {
        return (Import::MINLENGTH_NAME <= mb_strlen(keepOnlyChineseWord($columns[Import::I_NAME]), 'utf-8'));
    }

    protected function isAddressLengthEnough($columns)
    {
        return (Import::MINLENGTH_ADDRESS <= mb_strlen(keepOnlyChineseWord($columns[Import::I_ADDRESS]), 'utf-8'));
    }

    protected function isHometelLengthEnough($columns)
    {
        return (Import::MINLENGTH_CELLPHONE < strlen(keepOnlyNumber(nfTowf($columns[Import::I_HOMETEL], 0)), 'utf-8'));
    }

    protected function isCellPhoneLengthEnough($columns)
    {
        return (Import::MINLENGTH_TEL < mb_strlen(keepOnlyNumber(nfTowf($columns[Import::I_CELLPHONE], 0)), 'utf-8'));
    }

    protected function isEmailValid($columns)
    {
        return (!filter_var(nfTowf($columns[Import::I_EMAIL], 0), FILTER_VALIDATE_EMAIL) === false);
    }
}