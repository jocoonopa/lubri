<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportColumnAdapter
{
	protected $columns;
    protected $options;
    protected $validator;
    protected $dataHolder;
    protected $filter;
    protected $memberListFlagMap;
    protected $periodFlagMap;
    protected $state;
    protected $insertFlagPairs = [];
    protected $updateFlagPairs = [];

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();

        $this->configureOptions($resolver);
        
        $this->setOptions($resolver->resolve($options));

        $this->validator         = new ImportColumnValidator;
        $this->dataHolder        = new ImportDataHolder;
        $this->filter            = new ImportFilter;

        $this->inflateInsertFlag()->inflateUpdateFlag();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(Import::OPTIONS_DISTINCTION)
            ->setRequired(Import::OPTIONS_CATEGORY)
            ->setRequired(Import::OPTIONS_INSERTFLAG)
            ->setRequired(Import::OPTIONS_UPDATEFLAG)
        ;
    }

    public function inject($columns)
    {
        if (!$this->isValid($columns)) {
            return false;
        }

        $this->filter->clearInnerState();

        $this->dataHolder
            ->setName($this->filter->getName($this->getColumn(Import::I_NAME)))
            ->setBirthday($this->filter->getBirthday($this->getColumn(Import::I_BIRTHDAY)))
            ->setZipcode($this->filter->getZipcode($this->getColumn(Import::I_ZIPCODE), $this->getColumn(Import::I_ADDRESS)))
            ->setState($this->filter->getState($this->getColumn(Import::I_ZIPCODE)))
            ->setCity($this->filter->getCity($this->filter->getInnerState()))
            ->setAddress($this->filter->getAddress($this->filter->getInnerState(), $this->getColumn(Import::I_ADDRESS)))    
            ->setStatus($this->filter->getStatus($this->dataHolder->getAddress(), $this->filter->getInnerState()))
            ->setCellphone($this->filter->getCellphone($this->getColumn(Import::I_CELLPHONE)))
            ->setHometel($this->filter->getHometel($this->getColumn(Import::I_HOMETEL)))
            ->setOfficetel($this->filter->getOfficetel($this->getColumn(Import::I_OFFICETEL)))
            ->setPeriod($this->filter->getPeriod($this->getColumn(Import::I_PERIOD)))
            ->setEmail($this->filter->getEmail($this->getColumn(Import::I_EMAIL)))
            ->setHospital($this->filter->getHospital($this->getColumn(Import::I_HOSPITAL)))
        ;

        return $this;
    }

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
        $container = [];

        foreach (explode(' ', array_get($this->getOptions(), $nameIndex)) as $pairString) {
            if (false === strpos($pairString, ':')) {
                continue;
            }

            $pair = explode(':', $pairString);

            $container[array_get($pair, 0)] = array_get($pair, 1);
        }

        return $container;
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