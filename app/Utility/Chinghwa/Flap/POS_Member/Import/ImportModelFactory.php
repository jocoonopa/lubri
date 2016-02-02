<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

use App\Model\Flap\PosMemberImportTaskContent;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;

class ImportModelFactory
{
    /**
     * @return mixed 
     */
    protected function fetchExistOrEmpty(ImportDataHolder $dataHolder)
    {
        return array_get(Processor::getArrayResult($this->getExistOrEmptyQuery($dataHolder)), 0);
    }

    protected function getExistOrEmptyQuery(ImportDataHolder $dataHolder)
    {
        return Processor::table('POS_Member')
            ->select('TOP 1 *')
            ->where('Name', '=', $dataHolder->getName())
            ->where('Code', 'LIKE', 'T%')
            ->where(function ($q) use ($dataHolder) {
                $q
                    ->orWhere(function($q) use ($dataHolder) {
                        $q
                            ->where('LEN(Cellphone)', '>', Import::MINLENGTH_CELLPHONE)
                            ->where('Cellphone', '=', $dataHolder->getCellphone())
                        ;
                    })
                    ->orWhere(function($q) use ($dataHolder) {
                        $q
                            ->where('LEN(HomeTel)', '>', Import::MINLENGTH_TEL)
                            ->where('HomeTel', '=', $dataHolder->getHometel())
                        ;
                    })
                    ->orWhere(function($q) use ($dataHolder) {
                        $q
                            ->where('LEN(HomeAddress)', '>', Import::MINLENGTH_ADDRESS)
                            ->where('HomeAddress', '=', $dataHolder->getAddress())
                        ;
                    })
                ;
            })
            ->orderBy('SerNo', 'DESC');
    }

    public function create(ImportColumnAdapter $adapter)
    {
        $model = new PosMemberImportTaskContent;
        $dataHolder = $adapter->getDataHolder();
        list($serNo, $code, $serNoI) = $this->fetchExistOrEmpty($dataHolder);

        $model->serno              = $serNo;
        $model->code               = $code;
        $model->sernoi             = $serNoI;
        $model->name               = $dataHolder->getName();
        $model->cellphone          = $dataHolder->getCellphone();
        $model->hometel            = $dataHolder->getHometel();
        $model->officetel          = $dataHolder->getOfficetel();
        $model->birthday           = $dataHolder->getBirthday();
        $model->zipcode            = $dataHolder->getZipcode();
        $model->city               = $dataHolder->getCity();
        $model->state              = $dataHolder->getState();
        $model->homeaddress        = $dataHolder->getAddress();
        $model->distinction        = $adapter->getOptions()['distinction'];
        $model->category           = $adapter->getOptions()['category'];
        $model->period_at          = new \DateTime($dataHolder->getPeriod());
        $model->hospital           = $dataHolder->getHospital();
        $model->memo               = $dataHolder->getMemo();
        $model->sex                = Import::FEMALE_SEX_CODE;
        $model->flags              = json_encode((empty($serNo)) ? $adapter->getInsertFlagPairs() : $adapter->getUpdatelagPairs());
        $model->is_exist           = !empty($serNo);

        return $model;
    }
}
