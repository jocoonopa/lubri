<?php

namespace App\Export\FV\Import;

/**
 * Fetch all member into an export file, limit per select count under 1500
 */
class MemberExportHandler extends FVImportExportHandler
{
    const PROCESS_NAME = 'FVMemberImport';
}