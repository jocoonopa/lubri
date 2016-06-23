<?php

namespace App\Export\FVImport;

use Carbon\Carbon;

class MemberExport extends \Maatwebsite\Excel\Files\NewExcelFile
{
    protected $commend;
    protected $ouput;
    protected $size;
    protected $serno;
    protected $startAt;
    protected $endAt;
    protected $info;
    protected $upSerNo;
    public $isBig5 = false;
    public $nobom = false;
    public $limit;

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

    /**
     * Gets the value of sernoi.
     *
     * @return mixed
     */
    public function getSerno()
    {
        return $this->serno;
    }

    /**
     * Sets the value of sernoi.
     * MEMBR000,000,000,000,402020716
     * 
     * @param mixed $sernoi the sernoi
     *
     * @return self
     */
    public function setSerno($serno)
    {
        $this->serno = $serno;

        return $this;
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
     * Gets the value of upSerNo.
     *
     * @return mixed
     */
    public function getUpSerNo()
    {
        return $this->upSerNo;
    }

    /**
     * Sets the value of upSerNo.
     *
     * @param mixed $upSerNo the up ser no
     *
     * @return self
     */
    public function setUpSerNo($upSerNo)
    {
        $this->upSerNo = $upSerNo;

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
     * Gets the value of nobom.
     *
     * @return mixed
     */
    public function getNobom()
    {
        return $this->nobom;
    }

    /**
     * Sets the value of nobom.
     *
     * @param mixed $nobom the nobom
     *
     * @return self
     */
    public function setNobom($nobom)
    {
        $this->nobom = $nobom;

        return $this;
    }

    /**
     * Gets the value of limit.
     *
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Sets the value of limit.
     *
     * @param mixed $limit the limit
     *
     * @return self
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }
}