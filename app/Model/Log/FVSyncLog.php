<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class FVSyncLog extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fvsynclog';

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_id', 'filepath', 'filename', 'ip', 'mrt_time', 'count', 'exec_cost'
    ];

    protected $casts = ['mrt_time' => 'datetime'];

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
