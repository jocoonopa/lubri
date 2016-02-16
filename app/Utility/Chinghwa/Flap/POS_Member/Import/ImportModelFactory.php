<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

use App\Model\Flap\PosMemberImportTaskContent;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;

class ImportModelFactory
{
    /**
     * @return mixed 
     */
    public function fetchExistOrEmpty(ImportDataHolder $dataHolder)
    {
        return array_get(Processor::getArrayResult($this->_getExistOrEmptyQuery($dataHolder)), 0);
    }

    /**
     * @return mixed 
     */
    public static function getExistOrNotByContent(PosMemberImportTaskContent $content)
    {
        return array_get(Processor::getArrayResult(self::_getExistOrEmptyQueryStatic($content->name, $content->cellphone, $content->hometel, $content->homeaddress)), 0);
    }

    private function _getExistOrEmptyQuery(ImportDataHolder $dataHolder)
    {
        return $this->getExistOrEmptyQuery($dataHolder->getName(), $dataHolder->getCellphone(), $dataHolder->getHometel(), $dataHolder->getAddress());
    }

    private static function _getExistOrEmptyQueryStatic($name, $cellphone, $hometel, $address)
    {
        return Processor::table('POS_Member')
            ->select('TOP 1 SerNo, Code, MemberSerNoI')
            ->where('Name', '=', $name)
            ->where('Code', 'NOT LIKE', 'CT%')
            ->where(function ($q) use ($cellphone, $hometel, $address) {
                $q
                    ->orWhere(function($q) use ($cellphone) {
                        $q
                            ->where('LEN(Cellphone)', '>', Import::MINLENGTH_CELLPHONE)
                            ->where('Cellphone', '=', $cellphone)
                        ;
                    })
                    ->orWhere(function($q) use ($hometel) {
                        $q
                            ->where('LEN(HomeTel)', '>', Import::MINLENGTH_TEL)
                            ->where('HomeTel', '=', $hometel)
                        ;
                    })
                    ->orWhere(function($q) use ($address) {
                        $q
                            ->where('LEN(HomeAddress)', '>', Import::MINLENGTH_ADDRESS)
                            ->where('HomeAddress', '=', $address)
                        ;
                    })
                ;
            })
            ->orderBy('SerNo', 'DESC');
    }

    public function getExistOrEmptyQuery($name, $cellphone, $hometel, $address)
    {
        return Processor::table('POS_Member')
            ->select('TOP 1 SerNo, Code, MemberSerNoI')
            ->where('Name', '=', $name)
            ->where('Code', 'NOT LIKE', 'CT%')
            ->where(function ($q) use ($cellphone, $hometel, $address) {
                $q
                    ->orWhere(function($q) use ($cellphone) {
                        $q
                            ->where('LEN(Cellphone)', '>', Import::MINLENGTH_CELLPHONE)
                            ->where('Cellphone', '=', $cellphone)
                        ;
                    })
                    ->orWhere(function($q) use ($hometel) {
                        $q
                            ->where('LEN(HomeTel)', '>', Import::MINLENGTH_TEL)
                            ->where('HomeTel', '=', $hometel)
                        ;
                    })
                    ->orWhere(function($q) use ($address) {
                        $q
                            ->where('LEN(HomeAddress)', '>', Import::MINLENGTH_ADDRESS)
                            ->where('HomeAddress', '=', $address)
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

        list($serNo, $code, $serNoI) = (NULL !== ($memberData = $this->fetchExistOrEmpty($dataHolder))) ? array_values($memberData) : NULL;

        $model->serno              = $serNo;
        $model->code               = $code;
        $model->sernoi             = $serNoI;
        $model->name               = $dataHolder->getName();
        $model->email              = $dataHolder->getEmail();
        $model->cellphone          = $dataHolder->getCellphone();
        $model->hometel            = $dataHolder->getHometel();
        $model->officetel          = $dataHolder->getOfficetel();
        $model->birthday           = $dataHolder->getBirthday();
        $model->zipcode            = $dataHolder->getZipcode();
        $model->city               = $dataHolder->getCity();
        $model->state              = $dataHolder->getState();
        $model->homeaddress        = $dataHolder->getAddress();
        $model->distinction        = $adapter->getOptions()[Import::OPTIONS_DISTINCTION];
        $model->category           = $adapter->getOptions()[Import::OPTIONS_CATEGORY];
        $model->period_at          = (empty($dataHolder->getPeriod())) ? NULL : new \DateTime($dataHolder->getPeriod());
        $model->hospital           = $dataHolder->getHospital();        
        $model->sex                = Import::FEMALE_SEX_TEXT;
        $model->flags              = json_encode($this->_getFlags($adapter, $model));
        $model->status             = $dataHolder->getStatus();
        $model->memo               = $model->genMemo();
        $model->is_exist           = !empty($serNo);

        return $model;
    }

    private function _getFlags(ImportColumnAdapter $adapter, PosMemberImportTaskContent $model)
    {
        $flags = empty($model->serno) ? $adapter->getInsertFlagPairs() : $adapter->getUpdateFlagPairs();
        $flags['4'] = 'N';
        $flags['5'] = 'N';
        $flags['8'] = 'Y';
        $flags['23'] = ($model->period_at) ? array_get(PosMemberImportTaskContent::getPeriodFlagMap(), $model->period_at->format('Ym'), 'B') : 'A';

        return $flags;
    }
}