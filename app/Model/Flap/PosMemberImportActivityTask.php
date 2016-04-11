<?php

namespace App\Model\Flap;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Flap\CCS_MemberFlags\Flater;
use App\Utility\Chinghwa\Flap\ORM\BasicDataDef;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PosMemberImportActivityTask extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pos_member_import_act';

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
        'category'
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
          'name' => $ydName .  '_' . (PosMemberImportActivityTask::where('name', 'LIKE', "{$ydName}%")->count() + 1)
        ]), true);

        parent::__construct($attributes);
    }

    /**
     * A user can have many articles
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function content()
    {
        return $this->hasMany('App\Model\Flap\PosMemberImportActivityTaskContent');
    }

    /**
     * An article is owned by a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Model\User');
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
        $container = [];

        foreach (explode(' ', $flagString) as $pairString) {
            if (false === strpos($pairString, ':')) {
                continue;
            }

            $pair = explode(':', $pairString);

            $container[Flater::genKey(array_get($pair, 0))] = array_get($pair, 1);
        }

        return $container;
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

    private function _getFlagString($flags)
    {
        if (NULL === $flags) {
            return '';
        }

        $str = '';

        foreach ($flags as $key => $flag) {
            $str .= Flater::resoveKey($key) . ':' . $flag . ' ';
        }

        return substr($str, 0, -1);
    }
}
