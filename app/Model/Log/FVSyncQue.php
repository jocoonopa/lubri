<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class FVSyncQue extends Model
{
    const STATUS_INIT      = 0;
    const STATUS_WRITING   = 1;
    const STATUS_IMPORTING = 2;
    const STATUS_COMPLETE  = 3;
    const STATUS_EXCEPTION = 100;
    const STATUS_SKIP      = 10;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fvsyncque';

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_id', 'dest_file', 'status_code', 'last_modified_at', 'count', 'select_cost_time', 'import_cost_time'
    ];

    protected $casts = ['last_modified_at' => 'datetime'];

    /**
     * An article is owned by a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('App\Model\Log\FVSyncType');
    }
}
