<?php

namespace App\Export\FVImport;

use Carbon\Carbon;

class MemberExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    protected $commend;
    protected $ouput;
    protected $size = 500;
    protected $startAt;
    protected $endAt;
    protected $info;

    public function getFilename()
    {   
        return 'Import_Member_' . time();
    }

    /**
     * Gets the value of startAt.
     *
     * @return mixed
     */
    public function getStartAt()
    {
        return $this->startAt;
    }

    /**
     * Sets the value of startAt.
     *
     * @param mixed $startAt the start at
     *
     * @return self
     */
    public function setStartAt($startAt)
    {
        $this->startAt = $startAt;

        return $this;
    }

    /**
     * Gets the value of endAt.
     *
     * @return mixed
     */
    public function getEndAt()
    {
        return $this->endAt;
    }

    /**
     * Sets the value of endAt.
     *
     * @param mixed $endAt the end at
     *
     * @return self
     */
    public function setEndAt($endAt)
    {
        $this->endAt = $endAt;

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
     * Gets the value of size.
     *
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Sets the value of size.
     *
     * @param mixed $size the size
     *
     * @return self
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * Gets the value of ouput.
     *
     * @return mixed
     */
    public function getOutput()
    {
        return $this->ouput;
    }

    /**
     * Sets the value of ouput.
     *
     * @param mixed $ouput the ouput
     *
     * @return self
     */
    public function setOutput($ouput)
    {
        $this->ouput = $ouput;

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
}