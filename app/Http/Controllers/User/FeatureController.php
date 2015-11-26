<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;

class FeatureController extends Controller
{
    public function import()
    {
        // $insertRows = [];

        // $this->odbcFetchArray($this->getUserDataQuery(), $this->getInsertFunc(), $insertRows);

        // foreach ($insertRows as $row) {
        //     $this->insert($row);
        // }

        // return;
    }

    public function updateIpExt()
    {
        $self = $this;

        Excel::load(__DIR__ . '/../../../../storage/excel/example/ip_ext.xls', function($reader) use ($self) {
            $results = $reader->get();

            foreach($results as $row) {
                foreach ($row as $cell) {
                    $data = explode("\n", $cell);

                    if (!isset($data[0])) {
                        continue;
                    }
                    
                    if ($self->isIp($data[0])) {
                        continue;
                    }

                    if (!$self->isNameExtMixed($data)) {
                        continue;
                    }

                    // Find name , do update
                    $count = User::where('username', $self->getName($data))->count();

                    if (1 !== $count) {
                        continue;
                    }

                    $user = User::where('username', $self->getName($data))->first();
                    $user->ip = $self->getIp($data);
                    $user->ext = $self->getExt($data);
                    $user->save();
                }
            }
        });
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
        return !(array_key_exists(1, $data)) 
            ? NULL 
            : ($this->isIp($data[1]) ? $data[1] : NULL)
        ;
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

        $user->username = $row['UName'];
        $user->email = "{$row['Code']}@chinghwa.com.tw";
        $user->account = $row['Code'];
        $user->ip = NULL;
        $user->corp = $row['CName'];
        $user->password = 'WHATEVER';

        $user->save();
    }

    protected function getUserDataQuery()
    {
        return file_get_contents(__DIR__ . '/../../../../storage/sql/User/user.sql');
    }
}

