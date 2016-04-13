<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Lyin;

use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;

class Validator extends \App\Utility\Chinghwa\Flap\POS_Member\Validator
{
	public function isValid($columns)
	{
        return ($this->isNameLengthEnough($columns[Import::I_NAME]) 
            && (
                $this->isAddressLengthEnough($columns[Import::I_ADDRESS]) 
                || $this->isHometelLengthEnough($columns[Import::I_HOMETEL])
                || $this->isCellPhoneLengthEnough($columns[Import::I_CELLPHONE])
                || $this->isEmailValid($columns[Import::I_EMAIL])
            )
        );
	}
}