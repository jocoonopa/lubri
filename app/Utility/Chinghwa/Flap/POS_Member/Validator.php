<?php

namespace App\Utility\Chinghwa\Flap\POS_Member;

use App\Import\Flap\POS_Member\Import;

class Validator
{
    public function isNameLengthEnough($name)
    {
        return Import::MINLENGTH_NAME <= mb_strlen($name, Import::DOC_ENCODE);
    }

    public function isAddressLengthEnough($address)
    {
        return Import::MINLENGTH_ADDRESS <= mb_strlen(keepOnlyChineseWord($address), Import::DOC_ENCODE);
    }

    public function isHometelLengthEnough($hometel)
    {
        return Import::MINLENGTH_CELLPHONE < strlen(keepOnlyNumber(nfTowf($hometel, 0)));
    }

    public function isCellPhoneLengthEnough($cellphone)
    {
        return Import::MINLENGTH_TEL < strlen(keepOnlyNumber(nfTowf($cellphone, 0)));
    }

    public function isEmailValid($email)
    {
        return !(false === filter_var(nfTowf($email, 0), FILTER_VALIDATE_EMAIL));
    }
}