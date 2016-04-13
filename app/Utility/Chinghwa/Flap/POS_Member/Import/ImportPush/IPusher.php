<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportPush;

use App\Model\Flap\PosMemberImportContent;
use App\Model\Flap\PosMemberImportTask;

interface IPusher
{
    public function pushTask(PosMemberImportTask $task);
    public function pushContent(PosMemberImportContent $content);
    public function proc(PosMemberImportContent $content);
    public function updateProc(PosMemberImportContent $content);
    public function insertProc(PosMemberImportContent $content);
}