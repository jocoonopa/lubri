<?php

namespace App\Export\FV\Sync\Helper\Fetcher;

abstract class Fetcher
{
    protected $criteria;
    protected $options;

    abstract function get(array $options);

    /**
     * Gets the value of criteria.
     *
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Sets the value of criteria.
     *
     * @param mixed $criteria the criteria
     *
     * @return self
     */
    protected function setCriteria($criteria)
    {
        $this->criteria = $criteria;

        return $this;
    }

    /**
     * Gets the value of options.
     *
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets the value of options.
     *
     * @param mixed $options the options
     *
     * @return self
     */
    protected function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }
}