<?php

namespace App\Export\FV\Sync\Helper;

use App\Model\Log\FVSyncQue;

class ExecuteAgent
{
    public static function exec(FVSyncQue $que)
    {
        return self::command($que->viga_type, basename($que->dest_file));
    }

    public static function command($vigaType, $destFile)
    {
        $output = [];

        return exec(self::genCmd($vigaType, basename($destFile)), $output, $status);
    }

    public static function genCmd($vigaType, $destFile)
    {
        $cmd = '"' . env('VIG_EXE') . '" /d ' . env('VIG_SYS') . ' /agent $type $file';

        return str_replace(['$type', '$file'], [$vigaType, $destFile], $cmd);
    }
}