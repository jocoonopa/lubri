<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class FVSyncType extends Model
{
    protected $table = 'fvsynctype';

    protected $fillable = ['name', 'hname', 'depend_on_id'];

    public function log()
    {
        return $this->hasMany('App\Model\Log\FVSyncQue');
    }

    public function parent()
    {
        return $this->belongsTo('App\Model\Log\FVSyncType', 'depend_on_id');
    }

    public function children()
    {
        return $this->hasMany('App\Model\Log\FVSyncType', 'depend_on_id');
    }
}
