<?php

namespace App\Export\Mould;

use App\Model\State;

class FVMemberMould 
{
    protected $head =  [
        '會員代號', 
        '會員姓名', 
        '性別', 
        '生日', 
        '身份證號', 
        '連絡電話', 
        '公司電話', 
        '手機號碼', 
        '縣市', 
        '區', 
        '郵遞區號', 
        '地址', 
        'e-mail', 
        '開發人代號', 
        '開發人姓名', 
        '會員類別代號', 
        '會員類別名稱', 
        '區別代號', 
        '區別名稱', 
        '首次購物金額', 
        '首次購物日', 
        '最後購物金額', 
        '最後購物日', 
        '累積購物金額', 
        '累積紅利點數', 
        '輔翼會員參數', 
        '預產期', 
        '醫院',
        '旗標'
    ];

    public function getRow(array $member)
    {
        $this->replaceWithNewCityState($member);

        $hd = $this->getHospitalAndPeriod([array_get($member, '備註'), array_get($member, '備註1'), array_get($member, '備註2')]);

        $arr = [
            $this->transfer(array_get($member, '會員代號')),
            $this->transfer(array_get($member, '會員姓名')),
            $this->transfer(array_get($member, '性別')),
            $this->transfer(array_get($member, '生日')),
            $this->transfer(array_get($member, '身份證號')), 
            $this->transfer(array_get($member, '連絡電話')), 
            $this->transfer(array_get($member, '公司電話')), 
            $this->transfer(array_get($member, '手機號碼')),
            $this->transfer(array_get($member, '縣市')), 
            $this->transfer(array_get($member, '區')),
            $this->transfer(array_get($member, '郵遞區號')),
            $this->transfer(array_get($member, '地址')),
            $this->transfer(array_get($member, 'e-mail')),             
            $this->transfer(array_get($member, '開發人代號')),    //array_get($member, '開發人代號'),    
            $this->transfer(array_get($member, '開發人姓名')), //array_get($member, '開發人姓名'),
            $this->transfer(array_get($member, '會員類別代號')), 
            $this->transfer(array_get($member, '會員類別名稱')), 
            $this->transfer(array_get($member, '區別代號')),
            $this->transfer(array_get($member, '區別名稱')), 
            $this->transfer(array_get($member, '首次購物金額')),
            $this->transfer(array_get($member, '首次購物日')), 
            $this->transfer(array_get($member, '最後購物金額')),
            $this->transfer(array_get($member, '最後購物日')), 
            $this->transfer(array_get($member, '累積購物金額')),
            $this->transfer(array_get($member, '累積紅利點數')), 
            $this->transfer(array_get($member, '輔翼會員參數')),
            $this->transfer(array_get($hd, 'period')),
            $this->transfer(array_get($hd, 'hospital'))
        ];

        $this->injectFlag($arr, $member);

        return $arr;
    }

    protected function transfer($str)
    {
        return csvStrFilter(trim(nfTowf($str, 0)));
    }

    public function getHead()
    {
        return $this->head;
    }

    protected function replaceWithNewCityState(&$member) 
    {
        $state = State::findByZipcode(array_get($member, '郵遞區號'))->first();

        if ($state) {
            $member['縣市'] = $state->city()->first()->name;
            $member['區'] = $state->name;
        }
    }

    /**
     * 將旗標注入陣列
     * 
     * @param  array  &$arr  
     * @param  array  $member [Query member result array]
     * @return void
     */
    protected function injectFlag(array &$arr, array $member)
    {
        for ($i = 1; $i <= 40; $i ++) {
            $flag = "Distflags_{$i}";

            $flagChar = trim(array_get($member, $flag));

            $arr[] = $flagChar;
        }
    }

    /**
     * @deprecated v20160530 [Viga change import columns, flag need to seperate to 40 independent columns]
     */
    protected function genVigaFormatFlagStr($member)
    {
        $flagStr = '';

        for ($i = 1; $i <= 40; $i ++) {
            $flag = "Distflags_{$i}";

            $flagChar = substr(array_get($member, $flag), 0, 1);

            $flagStr .= empty($flagChar) ? '^' : $flagChar; 
        }

        return $flagStr;
    }

    protected function getHospitalAndPeriod($memos)
    {
        $arr = $this->convertMemoStrToArr($memos);

        return 4 > count($arr) ? $this->getResproto() : $this->fillRes($arr);
    }

    protected function convertMemoStrToArr(array $memos)
    {
        $arr = [];

        foreach ($memos as $memo) {
            $arr = explode(';', $memo);

            if (3 >= count($arr)) {
                $arr = [];

                continue;
            }

            break;
        }

        return $arr;
    }

    protected function getResproto()
    {
        return ['hospital' => '', 'period' => ''];
    }

    protected function fillRes(array $arr)
    {
        $res = $this->getResproto();

        foreach ($arr as $val) {
            if (false !== strpos($val, '生產醫院')) {
                $res['hospital'] = trim(str_replace('生產醫院:', '', $val));
            }

            if (false !== strpos($val, '預產期')) {
                $res['period'] = preg_replace('/[^0-9]/', '', $val);

                if (6 === mb_strlen($res['period'])) {
                    $res['period'] .= '01';
                }
            }
        }

        return $res;
    }
}