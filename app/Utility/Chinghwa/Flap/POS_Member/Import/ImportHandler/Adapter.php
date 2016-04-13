<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler;

use App\Import\Flap\POS_Member\Import;
use App\Model\Flap\PosMemberImportTask;
use App\Utility\Chinghwa\Flap\POS_Member\Filter;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\DataHolder;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Adapter
{
    protected $columns;
    protected $options;
    protected $validator;
    protected $dataHolder;
    protected $filter;
    protected $insertFlagPairs = [];
    protected $updateFlagPairs = [];

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();

        $this->configureOptions($resolver);
        
        $this->setOptions($resolver->resolve($options));
        $importKind = $this->getOptions()[Import::OPTIONS_TASK]->kind()->first();
        $validatorClass = $importKind->validator;

        $this->validator         = new $validatorClass;
        $this->dataHolder        = new DataHolder;
        $this->filter            = new Filter;

        $this->inflateInsertFlag()->inflateUpdateFlag();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(Import::OPTIONS_TASK)
            ->setRequired(Import::OPTIONS_DISTINCTION)
            ->setRequired(Import::OPTIONS_CATEGORY)
            ->setRequired(Import::OPTIONS_INSERTFLAG)
            ->setRequired(Import::OPTIONS_UPDATEFLAG)
        ;
    }

    public function inject($columns){}

    protected function inflateInsertFlag()
    {
        $this->setInsertFlagPairs($this->getInflateFlag(Import::OPTIONS_INSERTFLAG));

        return $this;
    }

    protected function inflateUpdateFlag()
    {
        $this->setUpdateFlagPairs($this->getInflateFlag(Import::OPTIONS_UPDATEFLAG));

        return $this;
    }

    protected function getInflateFlag($nameIndex)
    {        
        $flagString = array_get($this->getOptions(), $nameIndex);

        return PosMemberImportTask::getInflateFlag($flagString);
    }

    protected function isValid($columns)
    {
        return $this->setColumns($columns)->validator->isValid($this->getColumns());
    }

    /**
     * Gets the value of dataHolder.
     *
     * @return mixed
     */
    public function getDataHolder()
    {
        return $this->dataHolder;
    }

    /**
     * Gets the value of columns.
     *
     * @return mixed
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Gets the value of columns.
     *
     * @return mixed
     */
    public function getColumn($index)
    {
        return $this->columns[$index];
    }

    /**
     * Sets the value of columns.
     *
     * @param mixed $columns the columns
     *
     * @return self
     */
    protected function setColumns($columns)
    {
        $this->columns = $columns;

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

    /**
     * Gets the value of insertFlagPairs.
     *
     * @return mixed
     */
    public function getInsertFlagPairs()
    {
        return $this->insertFlagPairs;
    }

    /**
     * Sets the value of insertFlagPairs.
     *
     * @param mixed $insertFlagPairs the insert flag pairs
     *
     * @return self
     */
    protected function setInsertFlagPairs($insertFlagPairs)
    {
        $this->insertFlagPairs = $insertFlagPairs;

        return $this;
    }

    /**
     * Gets the value of updateFlagPairs.
     *
     * @return mixed
     */
    public function getUpdateFlagPairs()
    {
        return $this->updateFlagPairs;
    }

    /**
     * Sets the value of updateFlagPairs.
     *
     * @param mixed $updateFlagPairs the update flag pairs
     *
     * @return self
     */
    protected function setUpdateFlagPairs($updateFlagPairs)
    {
        $this->updateFlagPairs = $updateFlagPairs;

        return $this;
    }
}