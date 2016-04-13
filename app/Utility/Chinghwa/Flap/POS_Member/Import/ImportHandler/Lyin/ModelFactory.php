<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Lyin;

use App\Import\Flap\POS_Member\Import;
use App\Model\Flap\PosMemberImportContent;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\DataHolder;

class ModelFactory extends \App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\ModelFactory
{
    /**
     * The model method will be override in deifferent way between different import way
     * 
     * @param  \App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Lyin\Adapter $adapter   
     * @return PosMemberImportContent   $model
     */
    public function create($adapter)
    {
        $dataHolder = $adapter->getDataHolder();

        $memberData = $this->fetchExistOrEmpty($dataHolder);

        list($serNo, $code, $serNoI) = (NULL !== $memberData ? array_values($memberData) : NULL);
        
        $model = new PosMemberImportContent;
        
        $model->serno                     = $serNo;
        $model->code                      = $code;
        $model->sernoi                    = $serNoI;
        $model->name                      = DataHolder::getByProxy($dataHolder->getName());
        $model->email                     = DataHolder::getByProxy($dataHolder->getEmail());
        $model->cellphone                 = DataHolder::getByProxy($dataHolder->getCellphone());
        $model->hometel                   = DataHolder::getByProxy($dataHolder->getHometel());
        $model->officetel                 = DataHolder::getByProxy($dataHolder->getOfficetel());
        $model->birthday                  = DataHolder::getByProxy($dataHolder->getBirthday());
        $model->homeaddress               = DataHolder::getByProxy($dataHolder->getAddress());
        $model->hospital                  = DataHolder::getByProxy($dataHolder->getHospital());   
        $model->state_id                  = $this->_getStateId($dataHolder);
        $model->distinction               = $adapter->getOptions()[Import::OPTIONS_DISTINCTION];
        $model->category                  = $adapter->getOptions()[Import::OPTIONS_CATEGORY];        
        $model->pos_member_import_task_id = $adapter->getOptions()[Import::OPTIONS_TASK]->id;
        $model->period_at                 = $this->_getPeriodAt($dataHolder);             
        $model->sex                       = Import::FEMALE_SEX_TEXT;
        $model->flags                     = $model->getFlags();
        $model->memo                      = $model->genMemo();
        $model->is_exist                  = !empty($serNo);
        $model->fixStatus();

        return $model;
    }
}