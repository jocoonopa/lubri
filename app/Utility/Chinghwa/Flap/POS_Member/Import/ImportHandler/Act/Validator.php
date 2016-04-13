<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Act;

use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;

class Validator extends \App\Utility\Chinghwa\Flap\POS_Member\Validator
{
    public function isValid($columns)
    {
        return ($this->isNameLengthEnough($columns[Import::A_NAME]) 
            && (
                $this->isAddressLengthEnough($columns[Import::A_ADDRESS]) 
                || $this->isHometelLengthEnough($columns[Import::A_TEL])
                || $this->isCellPhoneLengthEnough($columns[Import::A_TEL])
                || $this->isEmailValid($columns[Import::A_EMAIL])
            )
        );
    }
}