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

        $this->filter = new ImportFilter;

        $this->content = $content;

        $this
            ->fixZipcode()
            ->fixStateAndCity()
            ->fixAddress()
            ->fixCellphone()
            ->fixHometel()
            ->fixOfficetel()
            ->fixStatus()
            ->fixFlag23WithPeriodAt()
            ->fixMemo()
            ->save()
        ;
    }

    protected function fixZipcode()
    {
        $this->content->zipcode = $this->filter->getZipcode($this->content->zipcode, $this->content->homeaddress);

        return $this;
    }

    protected function fixStateAndCity()
    {
        $this->content->state = $this->filter->getState($this->content->zipcode);
        $this->content->city = $this->filter->getCity($this->filter->getInnerState());

        return $this;
    }

    protected function fixAddress()
    {
        $this->content->homeaddress = $this->filter->getAddress($this->filter->getInnerState(), $this->content->homeaddress);

        return $this;
    }

    protected function fixFlag23WithPeriodAt()
    {
        $periodAt = $this->content->getPeriodAt();
        $flags = (array) json_decode($this->content->flags);
        $flags['23'] = ($this->content->period_at) 
            ? array_get(PosMemberImportTaskContent::getPeriodFlagMap(), $periodAt->format('Ym'), 'B') 
            : 'A'
        ;

        $this->content->flags = json_encode($flags);

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

    protected function fixStatus()
    {
        if (NULL === $this->content->pushed_at) {
            $this->content->status = $this->filter->getStatus($this->content->status, $this->filter->getInnerState());
        }

        return $this;
    }

    protected function fixMemo()
    {
        $this->content->memo = $this->content->genMemo();

        return $this;
    }

    protected function save()
    {
        $this->content->save();

        return $this;
    }
}