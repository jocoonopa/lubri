<?php

namespace App\Export\FV\Import;

use App\Export\Mould\FVMemberMould;

class MemberExport extends FVImportExport
{
    public function getType()
    {
        return 'member';
    }

    public function getMould()
    {
        return new FVMemberMould;
    }
}