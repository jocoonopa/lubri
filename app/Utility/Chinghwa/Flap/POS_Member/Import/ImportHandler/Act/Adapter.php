<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Act;

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
            ->setName($this->filter->getName($this->getColumn(Import::A_NAME)))
            ->setState($this->filter->getState('000', $this->getColumn(Import::A_ADDRESS)))
            ->setAddress($this->filter->getAddress($this->filter->getCacheState(), $this->getColumn(Import::A_ADDRESS)))    
            ->setStatus($this->filter->getStatus($this->dataHolder->getAddress(), $this->filter->getCacheState()))
            ->setCellphone($this->filter->getCellphone($this->getColumn(Import::A_TEL)))
            ->setHometel($this->filter->getHometel($this->getColumn(Import::A_TEL), $this->filter->getCacheState()))
            ->setEmail($this->filter->getEmail($this->getColumn(Import::A_EMAIL)))
            ->setSex($this->filter->getEmail($this->getColumn(Import::A_SEX)))
        ;

        return $this;
    }
}