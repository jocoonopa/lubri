<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class FVSyncType extends Model
{
    protected $table = 'fvsynctype';

    public function log()
    {
        return $this->hasMany('App\Model\Log\FVSyncQue');
    }
}
