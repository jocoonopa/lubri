<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Lyin;

use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;

class Adapter extends \App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Adapter
{
    public function inject($columns)
    {
        // isValid decide, and set columns
        if (!$this->isValid($columns)) {
            return false;
        }

        $this->filter->clearCacheState();

        $this->dataHolder
            ->setName($this->filter->getName($this->getColumn(Import::I_NAME)))
            ->setBirthday($this->filter->getBirthday($this->getColumn(Import::I_BIRTHDAY)))
            ->setState($this->filter->getState($this->getColumn(Import::I_ZIPCODE), $this->getColumn(Import::I_ADDRESS)))
            ->setAddress($this->filter->getAddress($this->filter->getCacheState(), $this->getColumn(Import::I_ADDRESS)))    
            ->setStatus($this->filter->getStatus($this->dataHolder->getAddress(), $this->filter->getCacheState()))
            ->setCellphone($this->filter->getCellphone($this->getColumn(Import::I_CELLPHONE)))
            ->setHometel($this->filter->getHometel($this->getColumn(Import::I_HOMETEL), $this->filter->getCacheState()))
            ->setOfficetel($this->filter->getOfficetel($this->getColumn(Import::I_OFFICETEL), $this->filter->getCacheState()))
            ->setPeriod($this->filter->getPeriod($this->getColumn(Import::I_PERIOD)))
            ->setEmail($this->filter->getEmail($this->getColumn(Import::I_EMAIL)))
            ->setHospital($this->filter->getHospital($this->getColumn(Import::I_HOSPITAL)))
        ;

        return $this;
    }
}