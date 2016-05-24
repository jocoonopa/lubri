<?php

namespace App\Export\FVSync;

use Carbon\Carbon;

class MemberExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    protected $max;
    protected $count;
    protected $info;
    protected $lastMrtTime;

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
     * Gets the value of max.
     *
     * @return mixed
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * Sets the value of max.
     *
     * @param mixed $max the max
     *
     * @return self
     */
    public function setMax($max)
    {
        $this->max = $max;

        return $this;
    }
}