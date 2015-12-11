<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use Maatwebsite\Excel\Facades\Excel;
use Session;

class FeatureController extends Controller
{
    public function import()
    {
        $insertRows = Processor::getArrayResult($this->getUserDataQuery());

        foreach ($insertRows as $row) {
            $this->insert($row);
        }

        Session::flash('success', '輔翼使用者資料匯入完成');

        return redirect('user');
    }

    public function updateIpExt()
    {
        $self = $this;

        Excel::selectSheets('全區')->load(__DIR__ . '/../../../../storage/excel/example/ip_ext.xls', $this->updateClosure());
        
        Session::flash('success', '全部人員<b>Ip</b>以及<b>分機</b>更新完成');

        return redirect('user');
    }

    protected function updateClosure()
    {
        return function ($worksheet) {
            $worksheet->each(function ($row) {
                foreach ($row as $cell) {
                    if (!$this->isDataValid($data = explode("\n", $cell))) {
                        continue;
                    }

                    if (!$this->isExistUsernameCountUnique($data)) {
                        continue;
                    }

                    $this->updateUser($data);
                }
            });
        };
    }

    protected function isDataValid(array $data)
    {
        return (isset($data[0]) && !$this->isIp($data[0]) && $this->isNameExtMixed($data));
    }

    protected function isExistUsernameCountUnique(array $data)
    {
        $count = User::where('username', $this->getName($data))->count();

        return 1 === $count;
    }

    protected function updateUser(array $data)
    {
        $user = User::where('username', $this->getName($data))->first();
        $user->ip = $this->getIp($data);
        $user->ext = $this->getExt($data);
        $user->save();

        return $user;
    }

    protected function isNameExtMixed(array $data)
    {
        return (NULL !== $this->getName($data) && NULL !== $this->getExt($data)); 
    }

    protected function isIp($str)
    {
        return !(false === filter_var($str, FILTER_VALIDATE_IP));
    }

    protected function getName(array $data)
    {
        return preg_replace('/[0-9]+/', '', $data[0]);
    }

    protected function getIp(array $data)
    {
        return !(array_key_exists(1, $data)) ? NULL : ($this->isIp($data[1]) ? $data[1] : NULL);
    }

    protected function getExt(array $data)
    {
        return preg_replace('/\D/', '', $data[0]);
    }

    protected function getInsertFunc() 
    {
        return function (&$insertRows, $row) {
            $insertRows[] = $row;
        };
    }

    protected function insert(array $row)
    {
        $user = new User;

        $user->setProfileByErpRow($row)->save();
    }

    protected function getUserDataQuery()
    {
        return file_get_contents(__DIR__ . '/../../../../storage/sql/User/user.sql');
    }
}

