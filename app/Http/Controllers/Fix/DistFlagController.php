<?php

namespace App\Http\Controllers\Fix;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mail;
use Maatwebsite\Excel\Facades\Excel;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Helper\Excel\ExcelHelper;

class DistFlagController extends Controller
{
    const COLUMN_ADDRESS_INDEX    = 4;
    const COLUMN_ADDRESS_INDEX_BK = 6;
    const DEFAULT_ADDRESS_STR     = '台灣省';
    const DEFAULT_ZIPCODE         = '000';
    const MEMBER_LIST_9_UP        = '104年9月上名單';
    const MEMBER_LIST_9_DOWN      = '104年9月下名單';
    const MEMBER_LIST_10_UP       = '104年10月上名單';
    const ENCODE_DEFAULT          = 'utf-8';
    const DEFAULT_TOWN            = '台灣省';

    protected $zipCodes = [];

    public function index()
    {
        $i = 0;
        $data = [];
        $self = $this;
        $this->setZipCodes();

        $startTime = microtime(true);

        Excel::selectSheetsByIndex(0)                         
            ->filter('chunk')
            ->load($_SERVER['DOCUMENT_ROOT'] . '/../storage/excel/import/hotfix_20151106.xlsx')
            ->chunk(500, function ($result) use (&$i, &$data, $self) {
                foreach ($result as $row) {
                    $mobile = str_replace('-' , '', getRowVal($row, ExcelHelper::rmi('C')));
                    $memo = getRowVal($row, ExcelHelper::rmi('H'));
                    
                    if (empty($mobile) || false === strpos($memo, $mobile)) {
                        $code = srp(getRowVal($row, ExcelHelper::rmi('A')));
                        $data[] = "{$memo};{$code}";
                    }

                    continue;
                }
            });

        $endTime = microtime(true);

        Excel::create('hotfix_20151106_tmp' . rand(), function ($excel) use ($data, $self) {
            $excel->sheet('sheet1', function ($sheet) use ($data, $self) {
                foreach ($data as $val) {
                    $tmpRow = $self->genTmpRow($val);
                    $sheet->appendRow($self->genTmpRow($val));
                }
            });
        })->store('xls', storage_path('excel/exports'));

        return view('basic.simple', [
            'title' => '寵兒名單地址dash修復', 
            'des' => '執行了' . floor($endTime - $startTime) . '秒',
            'res' => ''       
        ]);
    }

    protected function setZipCodes()
    {
        $this->zipCodes = json_decode($this->getThreeCodeJSON(), true);

        return $this;
    }

    protected function genTmpRow($val)
    {
        $tmp     = explode(';', $val);
        $address = trim($this->getAddress($tmp));
        $city    = $this->getCity($address);
        $town    = $this->getTown($city, $address);
        $zipcode = $this->getZipcode($city, $town);
        $road    = $this->getRoad($city, $town, $address);

        $tmp[] = $val;
        $tmp[] = $this->getFlagVal($tmp[0]);
        $tmp[] = $this->getDist($tmp[0]);
        $tmp[] = $city;
        $tmp[] = $town;
        $tmp[] = $road;
        $tmp[] = $zipcode;
        $tmp[] = "{$city}{$town}{$road}";

        return $tmp;
    }

    protected function getAddress(array $tmp)
    {
        if (6 < mb_strlen($tmp[self::COLUMN_ADDRESS_INDEX], self::ENCODE_DEFAULT)) {
            return $tmp[self::COLUMN_ADDRESS_INDEX];
        }

        if (6 < mb_strlen($tmp[self::COLUMN_ADDRESS_INDEX_BK], self::ENCODE_DEFAULT)) {
            return $tmp[self::COLUMN_ADDRESS_INDEX_BK];
        }

        return self::DEFAULT_ADDRESS_STR;
    }

    protected function getCity($address)
    {
        $city = mb_substr(trim($address), 0, 3, self::ENCODE_DEFAULT);

        return (array_key_exists($city, $this->zipCodes)) ? $city : self::DEFAULT_ADDRESS_STR;
    }

    protected function getTown($city, $address)
    {
        if (self::DEFAULT_ADDRESS_STR === $city) {
            return self::DEFAULT_TOWN;
        }

        $town = mb_substr($address, 3, 3, self::ENCODE_DEFAULT);

        return (array_key_exists($town, $this->zipCodes[$city])) ? $town : $this->proFixGetTown($city, $town);
    }

    protected function proFixGetTown($city, $town)
    {
        $townPreWords = mb_substr($town, 0, 2);

        if (array_key_exists($townPreWords, $this->zipCodes[$city])) {
            return $townPreWords;
        }

        foreach ($this->getTownTails() AS $tail) {
            $townRefact = "{$townPreWords}{$tail}";

            if (array_key_exists($townRefact, $this->zipCodes[$city])) {
                return $townRefact;
            }

            continue;
        }

        return self::DEFAULT_TOWN;
    }

    protected function getTownTails()
    {
        return ['市', '區', '鄉', '鎮'];
    }

    protected function getRoad($city, $town, $address)
    {
        return str_replace([$city, $town], [], $address);
    }

    protected function getZipcode($city, $town)
    {
        if (in_array(self::DEFAULT_TOWN, [$city, $town])) {
            return self::DEFAULT_ZIPCODE;
        }

        return $this->zipCodes[$city][$town];
    }

    protected function getFlagVal($str)
    {
        $val = '';

        switch (trim($str))
        {
            case self::MEMBER_LIST_9_UP:
                $val = 'R';
                break;
            case self::MEMBER_LIST_9_DOWN:
                $val = 'S';
                break;
            case self::MEMBER_LIST_10_UP:
                $val = 'T';
                break;
            default:
                $val = $str;
                break;
        }

        return $val;
    }

    protected function getDist($str)
    {
        $val = '';

        switch (trim($str))
        {
            case self::MEMBER_LIST_9_UP:
                $val = '126-69';
                break;
            case self::MEMBER_LIST_9_DOWN:
                $val = '126-69';
                break;
            case self::MEMBER_LIST_10_UP:
                $val = '126-70';
                break;
            default:
                $val = $str;
                break;
        }

        return $val;
    }
}