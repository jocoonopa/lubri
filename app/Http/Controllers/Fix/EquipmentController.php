<?php

namespace App\Http\Controllers\Fix;

use App\Http\Controllers\Controller;
use Mail;

class EquipmentController extends Controller
{
    const CHECK_TARGET  = 'TTL';
    const EMAIL_SUBJECT = '設備發現問題!';
    const LIMIT_DEPTH   = 5;
    const TTL_INDEX     = 2;

    /**
     * Check equipment process
     * 
     * @return mixed
     */
    public function ping()
    {
        $checkList = $this->getCheckList();

        $errList = $this->checkProcess($checkList);

        return (!empty($errList)) ? $this->mail($errList) : null;
    }

    /**
     * Check equipment process
     * 
     * @param  array $checkList  
     * @return array $errList
     */
    protected function checkProcess(array $checkList)
    {
        $errList = [];

        foreach ($checkList as $key => $ip) {
            if (false === $this->isWorked($ip)) {
                $errList[] = $ip . ' -- ' . $key;
            }
        }

        return $errList;
    }

    /**
     * Check whether equipment isWorked 
     * 
     * @param  string  $ip   
     * @param  integer $depth [recursive depths]
     * @return boolean       
     */
    protected function isWorked($ip, $depth = 0)
    {
        $output = null;
        $status = null;

        exec("ping {$ip} -n 1", $output, $status);

        if (!array_key_exists(self::TTL_INDEX, $output)) {
            return false;
        }

        // 用 TTL 的有無當判定
        if (false !== strpos($output[self::TTL_INDEX], self::CHECK_TARGET)) {
            return true;
        }

        return ($depth <= self::LIMIT_DEPTH) ? $this->isWorked($ip, ++ $depth) : false;   
    }

    /**
     * getCheckList
     * 
     * @return array
     */
    protected function getCheckList()
    {
        return [
            '防火牆1' => '192.168.10.36',
            '防火牆2' => '192.168.100.36',
            'CTI拋檔Y槽' => '192.168.100.5',
            'CTIDB' => '192.168.100.3',
            '神奇錄音系統' => '192.168.100.72',
            'AESServer' => '192.168.100.79',
            '交換機主機' => '192.168.100.71',
            'CTIAP' => '192.168.100.70',
            'FlapAp' => '192.168.100.68',
            'FlapDB' => '192.168.100.66',
            '宣揚POSAP' => '192.168.100.8',
            '宣揚POSDB' => '192.168.100.12',
            '酋長官網' => '192.168.100.17',
            'VM' => '192.168.11.41',
            'MailServer' => '192.168.10.2',
            'GateWay1' => '192.168.100.254',
            'GateWay2' => '192.168.172.254',
            'ADDNS' => '192.168.11.31'
            //'門禁系統' => '192.168.100.97'
        ];
    }

    /**
     * Email to employee
     * 
     * @return mixed
     */
    protected function mail(array $errList) 
    {
        return Mail::send('emails.errorInform', ['title' => self::EMAIL_SUBJECT, 'errList' => $errList], function ($m) {
            $m
                ->cc('tonyvanhsu@chinghwa.com.tw', '6820徐士弘')
                ->to('jeremy@chinghwa.com.tw', '6232游加恩')
                ->to('jocoonopa@chinghwa.com.tw', '6231小閎')
                ->subject(self::EMAIL_SUBJECT)
            ;
        });
    }
}