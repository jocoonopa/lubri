<?php

namespace App\Export\FV\Sync;

class OrderExportHandler extends FVSyncExportHandler
{
    /**
     * @override
     */
    protected function importFile($export) 
    {
        $output = [];
        
        return exec('"C:\Program Files (x86)\Pivotal\Relation\Relation.exe" /d ' . env('VIG_SYS') . ' /agent CHOrderSync ' . basename($export->getInfo()['file']), $output, $status);
    }

    protected function genExportFilePath($export)
    {
        if (!file_exists(env('FVSYNC_ORDER_STORAGE_PATH'))) {
            mkdir(env('FVSYNC_ORDER_STORAGE_PATH'), 0777, true);
        }
        
        return env('FVSYNC_ORDER_STORAGE_PATH') . $export->getType() . 'sync_export_' . time() . '.csv';
    }
}