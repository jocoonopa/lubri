<?php

namespace App\Model\Log;

use Illuminate\Database\Eloquent\Model;

class FVSyncQue extends Model
{
    const STATUS_INIT            = 0;
    const STATUS_WRITING         = 1;
    const STATUS_IMPORTING       = 2;
    const STATUS_COMPLETE        = 3;
    const STATUS_DELAY           = 4;
    const STATUS_DELAY_EXECUTING = 5;
    const STATUS_DELAY_COMPLETE  = 6;    
    const STATUS_EXCEPTION       = 100;
    const STATUS_DELAY_ERROR     = 101;
    const STATUS_SKIP            = 10;
    const USER_DEV_ID            = 89;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'fvsyncque';
    protected $connection = 'mysql2';

    public $timestamps = true;

    protected $attributes = [
        'creater_id' => self::USER_DEV_ID, 
        'conditions' => '[]'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type_id', 
        'dest_file', 
        'status_code', 
        'last_modified_at', 
        'count', 
        'select_cost_time', 
        'import_cost_time', 
        'creater_id', 
        'conditions'
    ];

    protected $casts = [
        'last_modified_at' => 'datetime',
        'conditions'       => 'array'
    ];

    /**
     * An article is owned by a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function type()
    {
        return $this->belongsTo('App\Model\Log\FVSyncType');
    }

    /**
     * An article is owned by a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function creater()
    {
        return $this->belongsTo('App\Model\User');
    }

    public function getStatusName()
    {
        $map = [
            self::STATUS_INIT            => '<span class="text-muted">建立中</span>',
            self::STATUS_WRITING         => '<span class="text-info">輔翼匯出中</span>',
            self::STATUS_IMPORTING       => '<span class="text-warning">匯入偉特中</span>',
            self::STATUS_COMPLETE        => '<span class="text-success">完成</span>',
            self::STATUS_DELAY           => '<span class="text-info">等待處理</span>',
            self::STATUS_DELAY_EXECUTING => '<span class="text-warning">處理中(指定)</span>',
            self::STATUS_DELAY_ERROR     => '<span class="text-danger">發生錯誤(指定)</span>',
            self::STATUS_DELAY_COMPLETE  => '<span class="text-success">完成(指定)</span>',
            self::STATUS_EXCEPTION       => '<span class="text-danger">發生錯誤</span>',
            self::STATUS_SKIP            => '<span class="text-muted">略過</span>'
        ];

        return array_get($map, $this->status_code);
    }

    public function getCompletedDateTime()
    {
        return in_array($this->status_code, [self::STATUS_COMPLETE, self::STATUS_EXCEPTION, self::STATUS_SKIP, self::STATUS_DELAY_ERROR, self::STATUS_DELAY_COMPLETE])
        ? $this->updated_at->format('Y-m-d H:i:s') : '';
    }

    public function scopeDelay($q)
    {
        $q->where('status_code', '=', self::STATUS_DELAY);
    }

    public function scopeDelayExecuting($q)
    {
        $q->where('status_code', '=', self::STATUS_DELAY_EXECUTING);
    }

    public function sculpDelay($typeId, array $conditions, $createId)
    {
        $this->type_id          = $typeId;
        $this->status_code      = self::STATUS_DELAY;
        $this->creater_id       = $createId;
        $this->conditions       = $conditions;
        $this->select_cost_time = 0;
        
        return $this;
    }
}
