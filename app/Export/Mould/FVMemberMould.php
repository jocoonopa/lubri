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

        return [
            array_get($member, '會員代號'),
            array_get($member, '會員姓名'),
            array_get($member, '性別'),
            array_get($member, '生日'),
            array_get($member, '身份證號'), 
            array_get($member, '連絡電話'), 
            array_get($member, '公司電話'), 
            array_get($member, '手機號碼'),
            array_get($member, '縣市'), 
            array_get($member, '區'),
            array_get($member, '郵遞區號'),
            array_get($member, '地址'),
            array_get($member, 'e-mail'),             
            array_get($member, 'AgentCD', array_get($member, '開發人代號')),    //array_get($member, '開發人代號'),    
            array_get($member, 'AgentName', array_get($member, '開發人姓名')), //array_get($member, '開發人姓名'),
            array_get($member, '會員類別代號'), 
            array_get($member, '會員類別名稱'), 
            array_get($member, '區別代號'),
            array_get($member, '區別名稱'), 
            array_get($member, '首次購物金額'),
            array_get($member, '首次購物日'), 
            array_get($member, '最後購物金額'),
            array_get($member, '最後購物日'), 
            array_get($member, '累積購物金額'),
            array_get($member, '累積紅利點數'), 
            array_get($member, '輔翼會員參數'),
            array_get($hd, 'period'),
            array_get($hd, 'hospital'),
            $this->genVigaFormatFlagStr($member)
        ];
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
            }
        }

        return $res;
    }
}