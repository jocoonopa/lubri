<?php

namespace App\Model\Flap;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Flap\CCS_MemberFlags\Flater;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PosMemberImportTask extends Model
{
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
        'error' => 'array',
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
