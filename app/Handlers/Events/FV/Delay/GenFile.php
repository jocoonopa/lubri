<?php

namespace App\Handlers\Events\FV\Delay;

use App;
use App\Events\FV\Delay\ExecEvent;
use App\Model\Log\FVSyncQue;

class GenFile
{
    protected $que;
    protected $writer;
    protected $fetcher;
    protected $export;

    public function handle(ExecEvent $event)
    {        
        try {
            $this->init();
            
            $fName = $this->getWriter()
                ->setDir($this->getStorageDir())
                ->write($this->fetchData())
                ->getFname()
            ;

            return $this->updateQueDestFile($fName);
        } catch (\Exception $e) {
            $this->errorHandle($event);
        }        
    }

    protected function init()
    {
        $this->setQue($event->getQue())->lock()->initWriter()->initFetcher()->initExport();
    }

    protected function getStorageDir()
    {
        return env($this->getExport()->getPathEnv());
    } 

    protected function fetchData()
    {
        return $this->getFetcher()->get($this->getQue()->conditions);
    }

    protected function lock()
    {
        $this->getQue()->status_code = FVSyncQue::STATUS_DELAY_EXECUTING;
        $this->getQue()->save();

        return $this;
    }

    protected function errorHandle(ExecEvent $event)
    {
        $event->getQue()->status_code = FVSyncQue::STATUS_DELAY_ERROR;
        $event->getQue()->save();
        $event->setError(true);
    }

    protected function updateQueDestFile($fName)
    {
        $this->getQue()->dest_file = $fName;
        $this->getQue()->save();

        return $this;
    }

    protected function initWriter()
    {
        return $this->setWriter(App::make('App\Export\FV\Sync\Helper\FileWriter\\' . ucfirst($this->getQue()->type->name) . 'FileWriter'));
    }

    protected function initFetcher()
    {
        return $this->setFetcher(App::make('App\Export\FV\Sync\Helper\Fetcher\\' . ucfirst($this->getQue()->type->name) . 'Fetcher'));
    }

    protected function initExport()
    {
        return $this->setExport(App::male('App\Export\FV\Sync\\' . ucfirst($this->getQue()->type->name) . 'Export'));
    }

    /**
     * Gets the value of fetcher.
     *
     * @return mixed
     */
    public function getFetcher()
    {
        return $this->fetcher;
    }

    /**
     * Sets the value of fetcher.
     *
     * @param mixed $fetcher the fetcher
     *
     * @return self
     */
    protected function setFetcher($fetcher)
    {
        $this->fetcher = $fetcher;

        return $this;
    }

    /**
     * Gets the value of writer.
     *
     * @return mixed
     */
    public function getWriter()
    {
        return $this->writer;
    }

    /**
     * Sets the value of writer.
     *
     * @param mixed $writer the writer
     *
     * @return self
     */
    protected function setWriter($writer)
    {
        $this->writer = $writer;

        return $this;
    }

    /**
     * Gets the value of que.
     *
     * @return mixed
     */
    public function getQue()
    {
        return $this->que;
    }

    /**
     * Sets the value of que.
     *
     * @param mixed $que the que
     *
     * @return self
     */
    protected function setQue($que)
    {
        $this->que = $que;

        return $this;
    }

    /**
     * Gets the value of export.
     *
     * @return mixed
     */
    public function getExport()
    {
        return $this->export;
    }

    /**
     * Sets the value of export.
     *
     * @param mixed $export the export
     *
     * @return self
     */
    protected function setExport($export)
    {
        $this->export = $export;

        return $this;
    }
}
