<?php 

namespace App\Export;

use App\Export\Mould\FVMould;

abstract class FileWriter
{
    protected $mould;
    protected $dir = 'excel/exports/ctilayout/';
    protected $fname;
    protected $file;

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
        $this->open()->put(bomstr());

        foreach ($data as $row) {
            $this->put("{$this->getEachRowStr($row)}\r\n");
        }

        $this->close();
    }

    public function open()
    {
        return $this->setFile(fopen($this->getFname(), 'w'));
    }

    public function put($str)
    {
        fwrite($this->getFile(), $str);
    }

    public function close()
    {
        return fclose($this->getFile());
    }

    public function getEachRowStr(array $row)
    {
        return implode(',', $this->getMould()->getRow($row));
    }

    public function mkdir()
    {
        if (!file_exists(storage_path($this->getDir()))) {
            mkdir(storage_path($this->getDir()), 0777, true);
        }

        return $this;
    }

    protected function genFileName()
    {
        return storage_path($this->getDir()) . with(new \ReflectionClass($this))->getShortName() . '_' .  time() . '.csv';
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

    /**
     * Gets the value of dir.
     *
     * @return mixed
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * Gets the value of file.
     *
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Sets the value of file.
     *
     * @param mixed $file the file
     *
     * @return self
     */
    protected function setFile($file)
    {
        $this->file = $file;

        return $this;
    }
}