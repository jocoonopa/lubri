<?php

namespace App\Utility\Chinghwa\Helper\Flap\PosMember;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;

class MemberCode
{
    const SERNO_PREFIX = 'MEMBR';
    const CODE_LENGTH  = 21;

	protected $code = '';

	public function setStartCode()
    {
        $query = 'SELECT TOP 1 MemberCardNo FROM POS_Member WHERE MemberCardNo LIKE \'T%\' ORDER BY Code DESC';
        
        $res = Processor::execErp($query);
        $member = odbc_fetch_array($res);

        $this->code = getArrayVal($member, 'MemberCardNo');

        return $this;
    }

    public function increCode()
    {
        $integerPart = (int) substr($this->code, 1);

        $this->code = substr($this->code, 0, 1) . ($integerPart + 1);

        return $this;
    }

    public function getCode()
    {
    	if ('' === $this->code) {
    		$this->setStartCode();
    	}

        return $this->code;
    }

    static public function genSerNoStr($serno)
    {
        return self::SERNO_PREFIX . str_pad($serno, self::CODE_LENGTH, '0', STR_PAD_LEFT);
    }

}