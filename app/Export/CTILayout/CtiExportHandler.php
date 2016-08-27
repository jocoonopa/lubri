<?php

namespace App\Export\CTILayout;

use App;
use App\Export\FV\Sync\Helper\Composer\ConditionComposer;

class CtiExportHandler extends ExportHandler
{
    protected function fetch()
    {
        return $this->getFetcher()->get(ConditionComposer::composeEngConditions());
    }

    protected function initFetcher()
    {
        return $this->setFetcher(App::make('App\Export\FV\Sync\Helper\Fetcher\ListFetcher'));
    }

    protected function initWriter()
    {
        return $this->setWriter(App::make('App\Export\FV\Sync\Helper\FileWriter\ListFileWriter'));
    }
}