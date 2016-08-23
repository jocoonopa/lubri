<?php 

namespace App\Export;

use App\Export\Mould\FVMould;

abstract class FileWriter
{
    const DIR_PATH = 'excel/exports/ctilayout/';
    protected $mould;
    protected $fname;

    /**
     * Class Constructor
     * @param    $mould   
     */
    public function __construct(FVMould $mould)
    {
        $this->setMould($mould)->mkdir()->setFname($this->genFileName());
    }

    public function write(array $data)
    {
        $file = fopen($this->getFname(), 'w');
        fwrite($file, bomstr());
        
        foreach ($data as $row) {
            fwrite($file, "{$this->getEachRowStr($row)}\r\n");
        }

        fclose($file);
    }

    protected function getEachRowStr(array $row)
    {
        return implode(',', $this->getMould()->getRow($row));
    }

    protected function mkdir()
    {
        if (!file_exists(storage_path(self::DIR_PATH))) {
            mkdir(storage_path(self::DIR_PATH), 0777);
        }

        return $this;
    }

    protected function genFileName()
    {
        return storage_path(self::DIR_PATH) . with(new \ReflectionClass($this))->getShortName() . '_' .  time() . '.csv';
    }

    /**
     * Gets the value of mould.
     *
     * @return mixed
     */
    public function getMould()
    {
        return $this->mould;
    }

    /**
     * Sets the value of mould.
     *
     * @param mixed $mould the mould
     *
     * @return self
     */
    protected function setMould($mould)
    {
        $this->mould = $mould;

        return $this;
    }

    /**
     * Gets the value of fname.
     *
     * @return mixed
     */
    public function getFname()
    {
        return $this->fname;
    }

    /**
     * Sets the value of fname.
     *
     * @param mixed $fname the fname
     *
     * @return self
     */
    protected function setFname($fname)
    {
        $this->fname = $fname;

        return $this;
    }
}