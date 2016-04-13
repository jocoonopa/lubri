<?php

namespace App\Utility\Chinghwa\Export;

use App\Model\Flap\PosMemberImportTask;
use App\Model\Flap\PosMemberImportContent;
use App\Utility\Chinghwa\Export\ImportTaskExport;
use App\Utility\Chinghwa\Flap\CCS_MemberFlags\Flater;
use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;

class ImportTaskExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    public function handle($export)
    {
        return $export->sheet('新客', function ($sheet) use ($export) {
            $this->attachProcess($export->getTask(), false, $sheet);
        })->sheet('舊客', function ($sheet) use ($export) {
            $this->attachProcess($export->getTask(), true, $sheet);
        })->store('xls', storage_path('excel/exports'), true);
    }

    protected function attachProcess(PosMemberImportTask $task, $isExist, $sheet)
    {
        $sheet->appendRow($this->getHeadColumns());

        $this->getContens($task, $isExist)->chunk(Import::CHUNK_SIZE, $this->chunkCallBack($sheet));
    }

    protected function getContens(PosMemberImportTask $task, $isExist)
    {
        return $task->content()->where('is_exist', '=', $isExist);
    }

    protected function chunkCallBack($sheet) 
    {
        return function ($contents) use ($sheet) {
            $contents->each($this->eachCallback($sheet));
        };
    }

    protected function eachCallback($sheet)
    {
        return function ($content) use ($sheet) {
            $sheet->appendRow($this->getRowDataFromContent($content));
        };
    }

    protected function getHeadColumns()
    {
        $columns = [
            'PK',
            '客代',
            '會員類別',
            '會員區別',
            '姓名',
            '生日',
            '郵遞區號',
            '縣市',
            '區',
            '地址',
            '行動電話',
            '住家電話',
            '辦公電話',
            'Email',
            '預產期',
            '生產醫院',
            '備註'
        ];

        for($i = 1; $i <= 40; $i ++) {
            $columns[] = "旗標_{$i}";
        }

        return $columns;
    }

    protected function getRowDataFromContent(PosMemberImportContent $content)
    {
        $row = [
            $content->serno, 
            $content->code, 
            $content->pos_member_import_task->category,
            $content->pos_member_import_task->distinction,
            $content->name,            
            $content->birthday,
            $content->getZipcode(),
            $content->getCityName(),
            $content->getStateName(),
            $content->homeaddress,
            $content->cellphone,
            $content->hometel,
            $content->officetel,
            $content->email,
            $content->period_at,
            $content->hospital,
            $content->memo
        ];

        return array_merge($row, array_values($this->getContentFlags($content)));
    }

    protected function getContentFlags($content)
    {
        $flags = $content->getFlagPrototype();

        foreach ($content->flags as $key => $flag) {
            $flags[$key] = $flag;
        }

        return $flags;
    }
}