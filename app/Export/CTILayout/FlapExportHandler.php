<?php
/*
 * This file is extends of Class Command.
 *
 * (c) Jocoonopa <jocoonopa@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Export\CTILayout;

use App;
use App\Export\FV\Sync\Helper\Composer\ConditionComposer;

class FlapExportHandler extends ExportHandler
{
    public function fetch()
    {
        return $this->getFetcher()->get($this->composeCondition());
    }

    protected function composeCondition()
    {
        return ConditionComposer::composeMixedConditions();
    }

    protected function initFetcher()
    {
        return $this->setFetcher(App::make('App\Export\FV\Sync\Helper\Fetcher\MemberFetcher'));
    }

    protected function initWriter()
    {
        return $this->setWriter(App::make('App\Export\FV\Sync\Helper\FileWriter\MemberFileWriter'));
    }
}