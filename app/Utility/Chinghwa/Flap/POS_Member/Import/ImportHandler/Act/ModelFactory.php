<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import\ImportHandler\Act;

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
        
        $model = new PosMemberImportContent;
        $model->serno                     = array_get($memberData, 'cust_serno');
        $model->code                      = array_get($memberData, 'cust_id');
        $model->sernoi                    = array_get($memberData, 'cust_sernoI');
        $model->name                      = DataHolder::getByProxy($dataHolder->getName());
        $model->email                     = DataHolder::getByProxy($dataHolder->getEmail());
        $model->cellphone                 = DataHolder::getByProxy($dataHolder->getCellphone());
        $model->hometel                   = DataHolder::getByProxy($dataHolder->getHometel());
        $model->homeaddress               = DataHolder::getByProxy($dataHolder->getAddress()); 
        $model->state_id                  = $this->_getStateId($dataHolder);
        $model->distinction               = $adapter->getOptions()[Import::OPTIONS_DISTINCTION];
        $model->category                  = $adapter->getOptions()[Import::OPTIONS_CATEGORY];        
        $model->pos_member_import_task_id = $adapter->getOptions()[Import::OPTIONS_TASK]->id;    
        $model->sex                       = '女' === $dataHolder->getSex() ? Import::FEMALE_SEX_TEXT : Import::MALE_SEX_TEXT;
        $model->flags                     = !empty($model->serno) ? $model->getActFlags() : $model->getOrgFlags();
        $model->memo                      = array_get($memberData, 'ob_memo') . $adapter->getOptions()[Import::OPTIONS_TASK]->memo;
        $model->is_exist                  = !empty($model->serno);
        $model->fixStatus();

        $model->status = $model->status|208; // 預產期,醫院,生日直接算有

        return $model;
    }
}