<?php

namespace App\Http\Controllers\Fix;

use App\Http\Controllers\Controller;
use Mail;

class EquipmentController extends Controller
{
    const CHECK_TARGET = 'TTL';
    const EMAIL_SUBJECT = '設備發現問題!';
    const LIMIT_DEPTH = 5;

    public function ping()
    {
        $errList = [];
        $checkList = $this->getCheckList();

        foreach ($checkList as $key => $ip) {
            if (false === $this->isWorked($ip)) {
                $errList[] = $ip . ' -- ' . $key;
            }
        }

        if (empty($errList)) {
            return null;
        }

        return Mail::send('emails.errorInform', ['title' => self::EMAIL_SUBJECT, 'errList' => $errList], function ($m) {
            $m
                ->cc('tonyvanhsu@chinghwa.com.tw', '6820徐士弘')
                ->to('jeremy@chinghwa.com.tw', '6232游加恩')
                ->to('jocoonopa@chinghwa.com.tw', '6231小閎')
                ->subject(self::EMAIL_SUBJECT)
            ;
        });
    }

    protected function isWorked($ip, $depth = 0)
    {
        $output = null;

        exec("ping {$ip} -n 1", $output, $status);

        // 用 TTL 的有無當判定
        if (false !== strpos($output[2], self::CHECK_TARGET)) {
            return true;
        }

        return ($depth <= self::LIMIT_DEPTH) ? $this->isWorked($ip, ++ $depth) : false;   
    }

    protected function getCheckList()
    {
        return [
            '防火牆1' => '192.168.10.36',
            '防火牆2' => '192.168.100.36',
            'CTIAP' => '192.168.100.5',
            'CTIDB' => '192.168.100.3',
            '神奇錄音系統' => '192.168.100.72',
            'AESServer' => '192.168.100.79',
            '交換機主機' => '192.168.100.71',
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
        ];
    }
}