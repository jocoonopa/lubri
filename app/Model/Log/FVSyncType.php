<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class FVSyncType extends Model
{
    const ID_PRODUCT       = 1;
    const ID_MEMBER        = 2;
    const ID_ORDER         = 3;
    const ID_CAMPAIGN      = 4;
    const ID_LIST          = 5;
    const ID_CALLLOG       = 6;
    const VIGATYPE_PRODUCT = 'CHProductSync';
    const VIGATYPE_MEMBER  = 'CHContactSync';
    const VIGATYPE_ORDER   = 'CHOrderSync';
    const VIGATYPE_LIST    = 'CHCTISync';
    const VIGATYPE_CALLLOG = 'CHCallLogSync';

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
