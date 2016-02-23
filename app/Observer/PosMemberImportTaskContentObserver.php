<?php

namespace App\Observer;

use App\Model\City;
use App\Model\Flap\PosMemberImportTaskContent;
use App\Model\State;
use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportFilter;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportModelFactory;

class PosMemberImportTaskContentObserver 
{
    protected $content;
    protected $filter;

    public function updating(PosMemberImportTaskContent $content)
    {   
        PosMemberImportTaskContent::unsetEventDispatcher();

        $this->content = $content;
        
        $this->filter = new ImportFilter;

        $this
            ->fixCellphone()
            ->fixHometel()
            ->fixOfficetel()            
            ->fixFlag23WithPeriodAt()
            ->fixMemo()
            ->fixAddress()
            ->fixStatus()
            ->save()
        ;
    }

    protected function fixFlag23WithPeriodAt()
    {
        $this->content->fixFlag23WithPeriodAt();

        return $this;
    }

    protected function fixCellphone()
    {
        $this->content->cellphone = $this->filter->getCellphone($this->content->cellphone);

        return $this;
    }

    protected function fixHometel()
    {
        $this->content->hometel = $this->filter->getHometel($this->content->hometel);

        return $this;
    }

    protected function fixOfficetel()
    {
        $this->content->officetel = $this->filter->getOfficetel($this->content->officetel);

        return $this;
    }

    protected function fixMemo()
    {
        $this->content->memo = $this->content->genMemo();

        return $this;
    }

    protected function fixAddress()
    {
        $this->content->homeaddress = $this->filter->getAddress($this->content->state, $this->content->homeaddress);

        return $this;
    }

    protected function fixStatus()
    {
        $this->content->fixStatus();

        return $this;
    }

    protected function save()
    {
        $this->content->save();

        return $this;
    }
}