<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;
use App\Model\State;
use App\Model\City;

class ImportFilter
{
    protected $innerState;

    /**
     * Gets the value of state.
     *
     * @return mixed
     */
    public function getInnerState()
    {
        return $this->innerState;
    }

    /**
     * Sets the value of state.
     *
     * @param mixed $state the state
     *
     * @return self
     */
    public function setInnerState(State $state)
    {
        $this->innerState = $state;

        return $this;
    }

    public function clearInnerState()
    {
        $this->innerState = NULL;

        return $this;
    }

    /**
     * 僅保留中文字
     * 
     * @return string
     */
    public function getName($val)
    {
        return keepOnlyChineseWord($val);
    }

    public function getBirthday($val)
    {
        return $this->getFileredDate($val);
    }

    public function getPeriod($val)
    {
        return $this->getFileredDate($val);
    }

    /**
     * 處理流程:
     * 
     * 1. 字串的非漢字部分轉換為半形
     * 2. 僅保留字串數字部分
     * 3. 透過日期格式做驗證, 合法回傳字串，反之回傳 NULL
     *
     * @param  integer $index
     * @return mixed
     */
    public function getFileredDate($val)
    {
        $filteredDate = keepOnlyNumber(nfTowf($val, 0));

        return validateDate($filteredDate,'Ymd') ? $filteredDate : NULL;
    }

    public function getZipcode($val, $address)
    {
        $zipcode = $this->_getZipcode($val);

        return $this->_isValidZipcode($zipcode) ? $zipcode : $this->_getZipcodeByGuessAddress($address);
    }

    public function _getZipcode($val)
    {
        return substr(keepOnlyNumber(nfTowf($val, 0)), 0, Import::MINLENGTH_ZIPCODE);
    }

    /**
     * 判斷區碼是否合法
     *
     * 條件:
     * 
     * 1. 長度
     * 2. 可否從 State 找到符合的區
     *
     * 若合法，直接在這邊將取得的 State 存入屬性中,
     * 並且回傳 true
     *
     * 注意:
     *
     * 一定要確保
     * 
     * @param  string  $zipcode
     * @return boolean          
     */
    public function _isValidZipcode($zipcode)
    {
        if (Import::MINLENGTH_ZIPCODE !== strlen($zipcode)) {
            return false;
        }

        if (NULL !== ($state = State::findByZipcode($zipcode)->first())) {
            $this->setInnerState($state);

            return true;
        }

        return false;
    }

    public function _getZipcodeByGuessAddress($address)
    {
        $address = $this->getOriginAddress($address);

        if (NULL !== ($typeA = $this->_guessAddressTypeA($address))) {
            return $typeA;
        }

        if (NULL !== ($typeB = $this->_guessAddressTypeB($address))) {
            return $typeB;
        }  

        $this->clearInnerState();

        return Import::DEFAULT_ZIPCODE;
    }

    public function _guessAddressTypeA($address)
    {
        for ($i = 0; $i < 2; $i ++) {
            $addressPartial = keepOnlyNumber(mb_substr($address, $i * 3, 3, Import::DOC_ENCODE));

            if ($this->_isValidZipcode($addressPartial)) {
                return $addressPartial;
            }
        }

        return NULL;
    }

    public function _guessAddressTypeB($address)
    {
        $city = City::findByName(mb_substr($address, 0, 3, Import::DOC_ENCODE))->get()->first();

        if ($city) {
            $address3To9 = mb_substr($address, 3, 9, Import::DOC_ENCODE);

            foreach ($city->states as $state) {
                if ($state->isBelong($address3To9)) {
                    $this->setInnerState($state);

                    return $state->zipcode;
                }
            }
        }

        return NULL;
    }

    public function getAddress($state, $address)
    {        
        $replaces = (NULL === $state) 
            ? []
            : [
                $state->name, 
                $state->pastname, 
                $state->zipcode, 
                $state->city->name, 
                $state->city->pastname
            ];

        return str_replace($replaces, '', $this->getOriginAddress($address));
    }

    public function getOriginAddress($val)
    {        
        return strFilter(str_replace(['F', '-', "'", '"'], ['樓', '之', '', ''], trim(nfTowf($val))));
    }

    public function getCity($state)
    {
        return (NULL === $state) 
            ? Import::DEFAULT_CITYSTATE
            : $state->city->name
        ;
    }

    public function getState($zipcode)
    {
        $state = State::findByZipcode($zipcode)->first();

        return (NULL === $state) 
            ? Import::DEFAULT_CITYSTATE
            : $state->name
        ;
    }

    public function getStatus($address, $state)
    {
        $address = $this->getOriginAddress($address);
        $status = bindec('000000');
        
        if (0 !== mb_strlen($address, Import::DOC_ENCODE)) {
            $status = $this->_editStatus($status, bindec('000001'));
        }

        return (NULL === $state) ? $status : $this->_editStatus($status, bindec('000010'));
    }

    /**
     * @param  integer $status  
     * @param  integer $attached
     * @return integer          
     */
    public function _editStatus($status, $attached)
    {
        return $status|$attached;
    }

    /**
     * 0. 先全部全形轉半形且只保留數字部分
     * 
     * 電話號碼判斷
     * 
     * 1. 若沒有找到合法區碼，直接不處理回傳
     * 2. 若電話長度過短，直接回傳NULL
     * 
     * @param  string $tel
     * @return string     
     */
    public function getTel($tel, $state)
    {
        if ($this->isTelLengthValid($tel)) {
            return NULL;
        }

        $tel = keepOnlyNumber(nfTowf($tel));
    
        if (NULL === $state) {
            return $tel;
        }

        $params = $this->_getTelCodeRelateParams($state);

        $tel = $this->_getTelCodeAttachedTel($tel, $params);

        return $this->_getExtDelimeterAttachTel($tel, array_get($params, 3, 0));
    }

    public function isTelLengthValid($tel)
    {
        return Import::MINLENGTH_TEL <= strlen(keepOnlyNumber(nfTowf($tel)));
    }

    /**
     * 取得區碼處理相關所需參數
     * 
     * 台北,新北,基隆主碼部分8碼，其他7碼(02 === 02)
     * 
     * @return array
     */
    public function _getTelCodeRelateParams($state)
    {
        $telCode = $state->city->telcode;
        $telBodyLength = (Import::EIGHT_LENGTH_TELCODE === $telCode) ? Import::MINLENGTH_TEL + 1: Import::MINLENGTH_TEL;
        $telCodeLength = strlen($telCode);
        $telWithoutExtLength = $telBodyLength + $telCodeLength;

        return [$telCode, $telBodyLength, $telCodeLength, $telWithoutExtLength];
    }

    /**
     * 若區碼檢查合法，直接回傳電話號碼
     * 
     * 若
     *   1.長度比估計長度少1
     *   2.第一個數字不為 0, 
     *   3.電話前兩/三碼和區碼去0後相符
     *   
     *   表示電話其實有區碼，
     *   但因為Excel 判斷為數字把0拿掉了,
     *   因此則自動幫其補0
     *
     * 若目前電話長度為預估長度減去區碼長度, 且目前第一個號碼不為0則補區碼
     *   
     *   
     * @param  string $tel    
     * @param  array  $params 
     * @return string
     */
    public function _getTelCodeAttachedTel($tel, array $params)
    {
        list($telCode, $telBodyLength, $telCodeLength, $telWithoutExtLength) = $params;
        
        if (Import::TELCODE_HEAD === substr($tel, 0, 1)) {
            return (substr($tel, 0, $telCodeLength) === $telCode) ? $tel : Import:: WRONG_TELCODE . "{$tel}";
        }

        if (
            ($telWithoutExtLength - 1) === strlen($tel) 
            && Import::TELCODE_HEAD !== substr($tel, 0, 1) 
            && substr($telCode, 1) === substr($tel, 0, $telCodeLength - 1)
        ) {
            return Import::TELCODE_HEAD . $tel;
        }

        if (($telWithoutExtLength - $telCodeLength) === strlen($tel) && Import::TELCODE_HEAD !== substr($tel, 0, 1)) {
            return "{$telCode}{$tel}";
        }
    }

    /**
     * 若號碼包含分機, 前面補上 '-'
     * 
     * @param  string $tel                 
     * @param  integer $telWithoutExtLength 
     * @return string                      
     */
    public function _getExtDelimeterAttachTel($tel, $telWithoutExtLength = NULL)
    {
        if (0 === $telWithoutExtLength || Import:: WRONG_TELCODE === substr($tel, 0, strlen(Import:: WRONG_TELCODE))) {
            return $tel;
        }

        return $tel = (strlen($tel) > $telWithoutExtLength) 
            ? substr($tel, 0, $telWithoutExtLength) . Import::EXT_PREFIX . substr($tel, $telWithoutExtLength)
            : $tel;
    }

    /**
     * 判斷不合法但可修正狀況:
     * 
     * 0. 開頭為886, 轉0
     * 1. 開頭未補0, 如 987654321[可修正]
     * 
     * 判斷不合法且無法修正狀況:
     *
     * 2. 長度不足最小合法長度
     * 3. 長度為10, 但開頭不為0
     * 4. 長度超過10
     * 
     * @return string
     */
    public function getCellphone($val)
    {
        $cellPhone = $this->genProperFormatCellphone($val);

        return $this->isCellphoneLengthValid($cellPhone) ? $cellPhone : NULL;
    }

    public function genProperFormatCellphone($val)
    {
        $cellPhone = keepOnlyNumber(nfTowf($val));

        if (Import::CELLPHONE_ALTERHEAD === substr($cellPhone, 0, strlen(Import::CELLPHONE_ALTERHEAD))) {
            $cellPhone = str_replace_first(Import::CELLPHONE_ALTERHEAD, Import::CELLPHONE_HEADCHAR, $cellPhone);
        }

        if (Import::MINLENGTH_CELLPHONE === strlen($cellPhone)) {
            $cellPhone = Import::CELLPHONE_HEADCHAR . $cellPhone;
        }

        return $cellPhone;
    }

    public function isCellphoneLengthValid($cellPhone)
    {
        if (Import::MINLENGTH_CELLPHONE > strlen($cellPhone)) {
            return false;
        }

        if (Import::CELLPHONE_VALIDLENGTH === strlen($cellPhone) && Import::CELLPHONE_HEADCHAR !== substr($cellPhone, 0, 1)) {
            return false;
        }

        if (Import::CELLPHONE_VALIDLENGTH < strlen($cellPhone)) {
            return false;
        }

        return true;
    }

    public function getHometel($val)
    {
        return $this->getTel($val, $this->getInnerState());
    }

    public function getOfficetel($val)
    {
        return $this->getTel($val, $this->getInnerState());
    }

    public function getEmail($val)
    {
        return trim(nfTowf($val, 0));
    }

    public function getHospital($val)
    {
        return trim(nfTowf($val, 0));
    }
}