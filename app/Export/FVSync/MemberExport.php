<?php

namespace App\Export\FVSync;

use Carbon\Carbon;

class MemberExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    protected $que;
    protected $count;
    protected $info;
    protected $lastMrtTime;
    protected $chunkSize;
    protected $isBig5;
    protected $commend;
    protected $output;
    protected $importCostTime;
    protected $selectCostTime;

    public function getFilename()
    {   
        return 'Member_' . time();
    }

    public function getLastMrtTime()
    {
        return $this->lastMrtTime;
    }

    /**
     * Sets the value of lastMrtTime.
     *
     * @param mixed $lastMrtTime the last mrt time
     *
     * @return self
     */
    public function setLastMrtTime($lastMrtTime)
    {
        $this->lastMrtTime = $lastMrtTime;

        return $this;
    }

    /**
     * Gets the value of info.
     *
     * @return mixed
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Sets the value of info.
     *
     * @param mixed $info the info
     *
     * @return self
     */
    public function setInfo($info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * Gets the value of count.
     *
     * @return mixed
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Sets the value of count.
     *
     * @param mixed $count the count
     *
     * @return self
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Gets the value of chunkSize.
     *
     * @return mixed
     */
    public function getChunkSize()
    {
        return $this->chunkSize;
    }

    /**
     * Sets the value of chunkSize.
     *
     * @param mixed $chunkSize the chunk size
     *
     * @return self
     */
    public function setChunkSize($chunkSize)
    {
        $this->chunkSize = $chunkSize;

        return $this;
    }

    /**
     * Gets the value of isBig5.
     *
     * @return mixed
     */
    public function getIsBig5()
    {
        return $this->isBig5;
    }

    /**
     * Sets the value of isBig5.
     *
     * @param mixed $isBig5 the is big5
     *
     * @return self
     */
    public function setIsBig5($isBig5)
    {
        $this->isBig5 = $isBig5;

        return $this;
    }

    /**
     * Gets the value of commend.
     *
     * @return mixed
     */
    public function getCommend()
    {
        return $this->commend;
    }

    /**
     * Sets the value of commend.
     *
     * @param mixed $commend the commend
     *
     * @return self
     */
    public function setCommend($commend)
    {
        $this->commend = $commend;

        return $this;
    }

    /**
     * Gets the value of output.
     *
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Sets the value of output.
     *
     * @param mixed $output the output
     *
     * @return self
     */
    public function setOutput($output)
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Gets the value of importCostTime.
     *
     * @return mixed
     */
    public function getImportCostTime()
    {
        return $this->importCostTime;
    }

    /**
     * Sets the value of importCostTime.
     *
     * @param mixed $importCostTime the import cost time
     *
     * @return self
     */
    public function setImportCostTime($importCostTime)
    {
        $this->importCostTime = $importCostTime;

        return $this;
    }

    /**
     * Gets the value of selectCostTime.
     *
     * @return mixed
     */
    public function getSelectCostTime()
    {
        return $this->selectCostTime;
    }

    /**
     * Sets the value of selectCostTime.
     *
     * @param mixed $selectCostTime the select cost time
     *
     * @return self
     */
    public function setSelectCostTime($selectCostTime)
    {
        $this->selectCostTime = $selectCostTime;

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
    public function setQue($que)
    {
        $this->que = $que;

        return $this;
    }
}