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
    protected $connection = 'mysql2';

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

    public function getStatusName()
    {
        $map = [
            self::STATUS_INIT      => '<span class="text-muted">建立中</span>',
            self::STATUS_WRITING   => '<span class="text-info">輔翼匯出中</span>',
            self::STATUS_IMPORTING => '<span class="text-warning">匯入偉特中</span>',
            self::STATUS_COMPLETE  => '<span class="text-success">完成</span>',
            self::STATUS_EXCEPTION => '<span class="text-danger">發生錯誤</span>',
            self::STATUS_SKIP      => '<span class="text-muted">略過</span>'
        ];

        return array_get($map, $this->status_code);
    }

    public function getCompletedDateTime()
    {
        return in_array($this->status_code, [self::STATUS_COMPLETE, self::STATUS_EXCEPTION, self::STATUS_SKIP])
        ? $this->updated_at->format('Y-m-d H:i:s') : '';
    }
}
