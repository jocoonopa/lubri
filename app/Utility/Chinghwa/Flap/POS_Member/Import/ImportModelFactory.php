<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

use App\Model\Flap\PosMemberImportTask;
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

    public function create(ImportColumnAdapter $adapter, PosMemberImportTask $task)
    {
        $dataHolder = $adapter->getDataHolder();
        $memberData = $this->fetchExistOrEmpty($dataHolder);
        list($serNo, $code, $serNoI) = (NULL !== $memberData ? array_values($memberData) : NULL);
        $model = new PosMemberImportTaskContent;
        
        $model->serno                     = $serNo;
        $model->code                      = $code;
        $model->sernoi                    = $serNoI;
        $model->name                      = ImportDataHolder::getByProxy($dataHolder->getName());
        $model->email                     = ImportDataHolder::getByProxy($dataHolder->getEmail());
        $model->cellphone                 = ImportDataHolder::getByProxy($dataHolder->getCellphone());
        $model->hometel                   = ImportDataHolder::getByProxy($dataHolder->getHometel());
        $model->officetel                 = ImportDataHolder::getByProxy($dataHolder->getOfficetel());
        $model->birthday                  = ImportDataHolder::getByProxy($dataHolder->getBirthday());
        $model->homeaddress               = ImportDataHolder::getByProxy($dataHolder->getAddress());
        $model->hospital                  = ImportDataHolder::getByProxy($dataHolder->getHospital());   
        $model->state_id                  = $this->_getStateId($dataHolder);
        $model->distinction               = $adapter->getOptions()[Import::OPTIONS_DISTINCTION];
        $model->category                  = $adapter->getOptions()[Import::OPTIONS_CATEGORY];
        $model->period_at                 = $this->_getPeriodAt($dataHolder);             
        $model->sex                       = Import::FEMALE_SEX_TEXT;
        $model->pos_member_import_task_id = $task->id;
        $model->flags                     = $model->getFlags();
        $model->memo                      = $model->genMemo();
        $model->is_exist                  = !empty($serNo);
        $model->fixStatus();

        return $model;
    }

    private function _getStateId(ImportDataHolder $dataHolder)
    {
        $state = $dataHolder->getState();

        return (NULL === $state) ? $state : $state->id;
    }

    private function _getPeriodAt(ImportDataHolder $dataHolder)
    {
        return empty($dataHolder->getPeriod()) ? NULL : with(new \DateTime($dataHolder->getPeriod()))->format('Y-m-d');
    }
}