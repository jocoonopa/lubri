<?php

namespace App\Utility\Chinghwa\Flap\POS_Member\Import;

class ImportDataHolder
{
    protected $name;
    protected $birthday;
    protected $address;
    protected $zipcode;
    protected $city;
    protected $state;
    protected $cellphone;
    protected $hometel;
    protected $period;
    protected $email;
    protected $hospital;
    protected $officetel;
    protected $memo;
    protected $isExist;
    protected $flags;

    /**
     * Gets the value of address.
     *
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Sets the value of address.
     *
     * @param mixed $address the address
     *
     * @return self
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Gets the value of zipcode.
     *
     * @return mixed
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * Sets the value of zipcode.
     *
     * @param mixed $zipcode the zipcode
     *
     * @return self
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    /**
     * Gets the value of city.
     *
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the value of city.
     *
     * @param mixed $city the city
     *
     * @return self
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Gets the value of state.
     *
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the value of state.
     *
     * @param mixed $state the state
     *
     * @return self
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * Gets the value of cellphone.
     *
     * @return mixed
     */
    public function getCellphone()
    {
        return $this->cellphone;
    }

    /**
     * Sets the value of cellphone.
     *
     * @param mixed $cellphone the cellphone
     *
     * @return self
     */
    public function setCellphone($cellphone)
    {
        $this->cellphone = $cellphone;

        return $this;
    }

    /**
     * Gets the value of hometel.
     *
     * @return mixed
     */
    public function getHometel()
    {
        return $this->hometel;
    }

    /**
     * Sets the value of hometel.
     *
     * @param mixed $hometel the hometel
     *
     * @return self
     */
    public function setHometel($hometel)
    {
        $this->hometel = $hometel;

        return $this;
    }

    /**
     * Gets the value of period.
     *
     * @return mixed
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * Sets the value of period.
     *
     * @param mixed $period the period
     *
     * @return self
     */
    public function setPeriod($period)
    {
        $this->period = $period;

        return $this;
    }

    /**
     * Gets the value of email.
     *
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the value of email.
     *
     * @param mixed $email the email
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Gets the value of hospital.
     *
     * @return mixed
     */
    public function getHospital()
    {
        return $this->hospital;
    }

    /**
     * Sets the value of hospital.
     *
     * @param mixed $hospital the hospital
     *
     * @return self
     */
    public function setHospital($hospital)
    {
        $this->hospital = $hospital;

        return $this;
    }

    /**
     * Gets the value of officetel.
     *
     * @return mixed
     */
    public function getOfficetel()
    {
        return $this->officetel;
    }

    /**
     * Sets the value of officetel.
     *
     * @param mixed $officetel the officetel
     *
     * @return self
     */
    public function setOfficetel($officetel)
    {
        $this->officetel = $officetel;

        return $this;
    }

    /**
     * Gets the value of name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the value of name.
     *
     * @param mixed $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Gets the value of birthday.
     *
     * @return mixed
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     * Sets the value of birthday.
     *
     * @param mixed $birthday the birthday
     *
     * @return self
     */
    public function setBirthday($birthday)
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * Gets the value of memo.
     *
     * @return mixed
     */
    public function getMemo()
    {
        return $this->memo;
    }

    /**
     * Sets the value of memo.
     *
     * @param mixed $memo the memo
     *
     * @return self
     */
    public function setMemo($memo)
    {
        $this->memo = $memo;

        return $this;
    }

    /**
     * Gets the value of isExist.
     *
     * @return mixed
     */
    public function getIsExist()
    {
        return $this->isExist;
    }

    /**
     * Sets the value of isExist.
     *
     * @param mixed $isExist the is exist
     *
     * @return self
     */
    public function setIsExist($isExist)
    {
        $this->isExist = $isExist;

        return $this;
    }

    /**
     * Gets the value of flags.
     *
     * @return mixed
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Sets the value of flags.
     *
     * @param mixed $flags the flags
     *
     * @return self
     */
    public function setFlags($flags)
    {
        $this->flags = $flags;

        return $this;
    }
}