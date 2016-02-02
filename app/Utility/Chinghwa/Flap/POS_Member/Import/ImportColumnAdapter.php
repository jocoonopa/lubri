<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

use Symfony\Component\OptionsResolver\OptionsResolver;

class ImportColumnAdapter
{
	protected $columns;
    protected $options;
    protected $validator;
    protected $dataHolder;
    protected $zipcodeMap;
    protected $telCodeMap;
    protected $memberListFlagMap;
    protected $periodFlagMap;
    protected $insertFlagPairs = [];
    protected $updateFlagPairs = [];

    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();

        $this->configureOptions($resolver);
        
        $this->setOptions($resolver->resolve($options));

        $this->validator         = new ImportColumnValidator;
        $this->dataHolder        = new ImportDataHolder;
        $this->zipcodeMap        = $this->getJsonSrcArrayResult('zipcode');
        $this->telCodeMap        = $this->getJsonSrcArrayResult('telcode');
        $this->memberListFlagMap = $this->getJsonSrcArrayResult('memberlistFlagMap');
        $this->periodFlagMap     = $this->getJsonSrcArrayResult('periodFlagMap');

        $this->inflateInsertFlag()->inflateUpdateFlag();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(Import::OPTIONS_DISTINCTION)
            ->setRequired(Import::OPTIONS_CATEGORY)
            ->setRequired(Import::OPTIONS_INSERTFLAG)
            ->setRequired(Import::OPTIONS_UPDATEFLAG)
        ;
    }

    protected function getJsonSrcArrayResult($srcName)
    {
        return json_decode(file_get_contents(Import::STORAGE_PATH . "{$srcName}.json"), true);
    }

    public function inject($columns)
    {
        if (!$this->isValid($columns)) {
            return false;
        }

        $this->dataHolder
            ->setName($this->getFilteredName())
            ->setBirthday($this->getFilteredBirthday())
            ->setZipcode($this->getFilteredZipcode())
            ->setCity($this->getFilteredCity())
            ->setState($this->getFilteredState())
            ->setAddress($this->getFilteredAddress())    
            ->setCellphone($this->getFilteredCellphone())
            ->setHometel($this->getFilteredHometel())
            ->setOfficetel($this->getFilteredOfficetel())
            ->setPeriod($this->getFilteredPeriod())
            ->setEmail($this->getFilteredEmail())
            ->setHospital($this->getFilteredHospital())
            ->setMemo($this->getFilteredMemo())
        ;

        return $this;
    }

    /**
     * 僅保留中文字
     * 
     * @return string
     */
    protected function getFilteredName()
    {
        echo $this->getColumn(Import::I_NAME);

        return keepOnlyChineseWord($this->getColumn(Import::I_NAME));
    }

    protected function getFilteredBirthday()
    {
        return $this->getFileredDate(Import::I_BIRTHDAY);
    }

    protected function getFilteredPeriod()
    {
        return $this->getFileredDate(Import::I_PERIOD);
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
    protected function getFileredDate($index)
    {
        $filteredDate = keepOnlyNumber(nfTowf($this->getColumn($index), 0));

        return validateDate($filteredDate) ? $filteredDate : NULL;
    }

    protected function getFilteredZipcode()
    {
        return keepOnlyNumber(nfTowf($this->getColumn(Import::I_ZIPCODE), 0));
    }

    protected function getFilteredCity()
    {
        return array_search($this->getDataHolder()->getZipcode(), $this->zipcodeMap);
    }

    protected function getFilteredState()
    {
        return array_search($this->getDataHolder()->getCity(), $this->zipcodeMap);
    }

    protected function getFilteredAddress()
    {        
        return str_replace(['F', '-'], ['樓', '之'], trim(nfTowf($this->getColumn(Import::I_ADDRESS))));
    }

    protected function getFilteredTel($tel)
    {
        $tel = keepOnlyNumber(nfTowf($tel));
        echo 'tel:' . $tel;
        $telCode = array_get($this->telCodeMap, $this->getDataHolder()->getCity(), NULL);
        echo ', telcode:' . $telCode;
        // 補區碼
        // 是否已經存在區碼，若已經存在則不補
        $tel = (substr($tel, 0, strlen($telCode)) === $telCode) ? $tel : $telCode . $tel;
        echo 'realTel:' . $tel . "<br />";
        // 分機前面補-
        // 台北,新北,基隆主碼部分8碼，其他7碼
        $telBodyLength = (Import::EIGHT_LENGTH_TELCODE === $telCode) ? Import::MINLENGTH_TEL + 1: Import::MINLENGTH_TEL;
        $telWithoutExtLength = $telBodyLength + strlen($telCode);
        
        // 若號碼包含分機, 前面補上 '-'
        return $tel = (strlen($tel) > $telWithoutExtLength) 
            ? substr($tel, 0, $telWithoutExtLength) . Import::EXT_PREFIX . substr($tel, $telWithoutExtLength)
            : $tel;
    }

    protected function getFilteredCellphone()
    {
        $cellPhone = keepOnlyNumber(nfTowf($this->getColumn(Import::I_CELLPHONE)));

        return (Import::MINLENGTH_CELLPHONE === strlen($cellPhone)) ? "0{$cellPhone}" : $cellPhone;
    }

    protected function getFilteredHometel()
    {
        return $this->getFilteredTel($this->getColumn(Import::I_HOMETEL));
    }

    protected function getFilteredOfficetel()
    {
        return $this->getFilteredTel(nfTowf($this->getColumn(Import::I_OFFICETEL), 0));
    }

    protected function getFilteredEmail()
    {
        return trim(nfTowf($this->getColumn(Import::I_EMAIL), 0));
    }

    protected function getFilteredHospital()
    {
        return trim(nfTowf($this->getColumn(Import::I_HOSPITAL), 0));
    }

    protected function getFilteredMemo()
    {
        return array_get($this->memberListFlagMap, $this->getFlag12()) . ';' 
            . $this->getDataHolder()->getName() . ';' 
            . $this->getDataHolder()->getCellphone() . ';' 
            . $this->getDataHolder()->getCity() . $this->getDataHolder()->getState() . $this->getDataHolder()->getAddress(). ';' 
            . "預產期:{$this->getDataHolder()->getPeriod()}" . ';' 
            . "生產醫院:{$this->getDataHolder()->getHospital()}";
    }

    protected function inflateInsertFlag()
    {
        $this->setInsertFlagPairs($this->getInflateFlag(Import::OPTIONS_INSERTFLAG));

        return $this;
    }

    protected function inflateUpdateFlag()
    {
        $this->setUpdateFlagPairs($this->getInflateFlag(Import::OPTIONS_UPDATEFLAG));

        return $this;
    }

    protected function getInflateFlag($nameIndex)
    {
        $container = [];

        foreach (explode(' ', array_get($this->getOptions(), $nameIndex)) as $pairString) {
            if (false === strpos($pairString, ':')) {
                continue;
            }

            $pair = explode(':', $pairString);

            $container[array_get($pair, 0)] = array_get($pair, 1);
        }

        return $container;
    }

    protected function getFlag12()
    {
        pr($this->insertFlagPairs);
        return array_get($this->insertFlagPairs, 12);
    }

	protected function isValid($columns)
	{
        return $this->setColumns($columns)->validator->isValid($this->getColumns());
	}

    /**
     * Gets the value of dataHolder.
     *
     * @return mixed
     */
    public function getDataHolder()
    {
        return $this->dataHolder;
    }

    /**
     * Gets the value of columns.
     *
     * @return mixed
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Gets the value of columns.
     *
     * @return mixed
     */
    public function getColumn($index)
    {
        return $this->columns[$index];
    }

    /**
     * Sets the value of columns.
     *
     * @param mixed $columns the columns
     *
     * @return self
     */
    protected function setColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * Gets the value of options.
     *
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Sets the value of options.
     *
     * @param mixed $options the options
     *
     * @return self
     */
    protected function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Gets the value of insertFlagPairs.
     *
     * @return mixed
     */
    public function getInsertFlagPairs()
    {
        return $this->insertFlagPairs;
    }

    /**
     * Sets the value of insertFlagPairs.
     *
     * @param mixed $insertFlagPairs the insert flag pairs
     *
     * @return self
     */
    protected function setInsertFlagPairs($insertFlagPairs)
    {
        $this->insertFlagPairs = $insertFlagPairs;

        return $this;
    }

    /**
     * Gets the value of updateFlagPairs.
     *
     * @return mixed
     */
    public function getUpdateFlagPairs()
    {
        return $this->updateFlagPairs;
    }

    /**
     * Sets the value of updateFlagPairs.
     *
     * @param mixed $updateFlagPairs the update flag pairs
     *
     * @return self
     */
    protected function setUpdateFlagPairs($updateFlagPairs)
    {
        $this->updateFlagPairs = $updateFlagPairs;

        return $this;
    }
}