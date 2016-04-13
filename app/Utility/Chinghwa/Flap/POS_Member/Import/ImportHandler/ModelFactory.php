<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler;

use App\Model\Flap\PosMemberImportContent;
use App\Utility\Chinghwa\ORM\ERP\POS_Member;

class ModelFactory implements IModelFactory
{
    /**
     * The model method will be override in deifferent way between different import way
     * 
     * @param  \App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Adapter $adapter   
     * @return PosMemberImportContent   $model
     */
    public function create($adapter){}

    /**
     * @return mixed 
     */
    public function fetchExistOrEmpty(DataHolder $dataHolder)
    {
        return POS_Member::first([
            'name'      => $dataHolder->getName(),
            'cellphone' => $dataHolder->getCellphone(),
            'hometel'   => $dataHolder->getHometel(),
            'address'   => $dataHolder->getAddress()
        ]);
    }

    /**
     * @return mixed 
     */
    public static function getExistOrNotByContent(PosMemberImportContent $content)
    {
        return POS_Member::first([
            'name'      => $content->name,
            'cellphone' => $content->cellphone,
            'hometel'   => $content->hometel,
            'address'   => $content->homeaddress
        ]);
    }    

    protected function _getStateId(DataHolder $dataHolder)
    {
        $state = $dataHolder->getState();

        return (NULL === $state) ? $state : $state->id;
    }

    protected function _getPeriodAt(DataHolder $dataHolder)
    {
        return empty($dataHolder->getPeriod()) ? NULL : with(new \DateTime($dataHolder->getPeriod()))->format('Y-m-d');
    }
}