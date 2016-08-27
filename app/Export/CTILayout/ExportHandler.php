<?php

namespace App\Export\CTILayout;

abstract class ExportHandler implements \Maatwebsite\Excel\Files\ExportHandler 
{
    protected $writer;
    protected $fetcher;

    public function handle($export)
    {
        $this->initFetcher()->initWriter()->getWriter()->write($this->fetch());

        return $export->setFile($this->getWriter()->getFname());
    }

    abstract protected function fetch();
    abstract protected function initFetcher();
    abstract protected function initWriter();

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
}