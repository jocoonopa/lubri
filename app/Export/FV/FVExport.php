<?php

namespace App\Export\FV;

abstract class FVExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    protected $chunkSize;
    protected $commend;
    protected $output;
    protected $info;

    /**
     * Must be overrided
     */
    abstract public function getMould();

    /**
     * Decide which type of que would be use, must override this constant
     */
    abstract public function getType();

    public function getFilename()
    {
        return __CLASS__;
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
}