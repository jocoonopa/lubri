<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

use App\Model\Flap\PosMemberImportTaskContent;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Flap\CCS_MemberFlags\Flater;

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
        return self::_getExistOrEmptyQueryStatic($dataHolder->getName(), $dataHolder->getCellphone(), $dataHolder->getHometel(), $dataHolder->getAddress());
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
                            ->where('LEN(HomeAddress_Address)', '>', Import::MINLENGTH_ADDRESS)
                            ->where('HomeAddress_Address', '=', $address)
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
        $model->homeaddress        = $dataHolder->getAddress();

        $state = $dataHolder->getState();

        $model->state_id           = (NULL === $state) ? $state : $state->id;
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
        $flags = $this->_genFlagPrototype();

        $flags = empty($model->serno) ? array_merge($flags, $adapter->getInsertFlagPairs()) : $adapter->getUpdateFlagPairs();
        $flags[Flater::genKey(8)] = 'Y';
        $flags[Flater::genKey(23)] = ($model->period_at) ? array_get(PosMemberImportTaskContent::getPeriodFlagMap(), $model->period_at->format('Ym'), 'B') : 'A';

        return $flags;
    }

    private function _genFlagPrototype()
    {
        $flags = [];

        for ($i = 1; $i <= 40; $i ++) {
            $flags[Flater::genKey($i)] = 'N';
        }

        return $flags;
    }
}