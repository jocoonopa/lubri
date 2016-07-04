<?php

namespace App\Export\FV\Sync;

class MemberExportHandler extends FVSyncExportHandler
{
    /**
     * @override
     */
    protected function importFile($export) 
    {
        $output = [];
        
        return exec('"C:\Program Files (x86)\Pivotal\Relation\Relation.exe" /d ' . env('VIG_SYS') . ' /agent CHContactSync ' . basename($export->getInfo()['file']), $output, $status);
    }
}