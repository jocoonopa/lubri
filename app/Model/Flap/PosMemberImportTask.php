<?php

namespace App\Model\Flap;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Flap\CCS_MemberFlags\Flater;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PosMemberImportTask extends Model
{
    const STATUS_INIT       = 0;
    const STATUS_IMPORTING  = 1;
    const STATUS_TOBEPUSHED = 2;
    const STATUS_PUSHING    = 3;
    const STATUS_COMPLETED  = 4;
    const STATUS_PULLING    = 5;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pos_member_import_task';

    protected $attributes = ['category' => 126];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'user_id',
        'executed_at',
        'total_count',
        'status_code',
        'update_count',
        'insert_count',
        'error',
        'error_count',
        'import_cost_time',
        'execute_cost_time',
        'insert_flags',
        'update_flags',
        'distinction',
        'category',
        'memo',
        'kind_id'
    ];

    protected $casts = [
        'error'        => 'array',
        'insert_flags' => 'array',
        'update_flags' => 'array'
    ];

    public function __construct(array $attributes = [])
    {
        $ydName = Carbon::now()->format('Ym');

        $this->setRawAttributes(array_merge($this->attributes, [
          'name' => $ydName .  '_' . (PosMemberImportTask::where('name', 'LIKE', "{$ydName}%")->count() + 1)
        ]), true);

        parent::__construct($attributes);
    }

    /**
     * 取得任務狀態名稱
     * 
     * @return string
     */
    public function getStatusName()
    {
        $status = [
            '<span class="text-muted">建立中</span>', 
            '<span class="text-warning">匯入中</span>', 
            '<span class="text-primary">等待推送</span>', 
            '<span class="text-warning">推送中</span>', 
            '<span class="text-success">推送完成</span>', 
            '<span class="text-warning">同步中</span>'
        ];

        return array_get($status, $this->status_code, 'unknown');
    }

    /**
     * 判斷任務是否處於處理中狀態
     * 
     * @return boolean
     */
    public function isProgressing()
    {
        return in_array($this->status_code, [self::STATUS_INIT, self::STATUS_IMPORTING, self::STATUS_PUSHING, self::STATUS_PULLING]);
    }

    /**
     * A task can have many contents
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function content()
    {
        return $this->hasMany('App\Model\Flap\PosMemberImportContent');
    }

    /**
     * An task is owned by a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User');
    }

    /**
     * An task is owned by a task_kind
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kind()
    {
        return $this->belongsTo('App\Model\Flap\PosMemberImportKind');
    }

    public static function getBDSerNo($str)
    {
        $q = Processor::table('BasicDataDef')
            ->select('TOP 1 BDSerNo')
            ->where('BDCode', '=', $str)
        ;

        return array_get(Processor::getArrayResult($q), '0.BDSerNo');
    }

    public static function getCategorySerNo($str)
    {
        $q = Processor::table('POS_MemberCategory')
            ->select('TOP 1 SerNo')
            ->where('Code', '=', $str)
        ;

        return array_get(Processor::getArrayResult($q), '0.SerNo');
    }

    public static function getInflateFlag($flagString)
    {        
        return Flater::getInflateFlag($flagString);
    }

    private function _getFlagString($flags)
    {
        return Flater::getFlagString($flags);
    }

    public function updateStat()
    {
        $this->insert_count = $this->content()->where('is_exist', '=', false)->count();
        $this->update_count = $this->content()->where('is_exist', '=', true)->count();

        return $this;
    }

    public function getInsertFlagString()
    {
        return $this->_getFlagString($this->insert_flags);
    }

    public function getUpdateFlagString()
    {
        return $this->_getFlagString($this->update_flags);
    }    

    public function scopeFindByKind($query, $kind_id)
    {
        $query->where('kind_id', '=', $kind_id);
    }
}
