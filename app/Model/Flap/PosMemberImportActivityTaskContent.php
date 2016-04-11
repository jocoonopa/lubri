<?php

namespace App\Model\Flap;

use App\Utility\Chinghwa\Flap\CCS_MemberFlags\Flater;
use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportContent\StatusHandler;
use App\Utility\Chinghwa\Flap\POS_Member\Import\ImportContent\StatusRequest;
use DB;
use Illuminate\Database\Eloquent\Model;


class PosMemberImportActivityTaskContent extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pos_member_import_act_content';

    public $timestamps = true;

    protected $attributes = ['sex' => 'female'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'serno', 
        'name', 
        'code', 
        'sernoi', 
        'email',
        'pos_member_import_act_id',
        'cellphone',
        'hometel',
        'officetel',
        'state_id',
        'homeaddress',
        'birthday',
        'salepoint_serno',
        'employee_serno',
        'distinction',
        'exploit_serno',
        'exploit_emp_serno',
        'member_level_ec',
        'employ_code',
        'category',
        'period_at',
        'hospital',
        'memo',
        'sex',
        'flags',
        'is_exist',
        'pushed_at',
        'status'
    ];

    protected $casts = [
        'is_exist' => 'boolean',
        'flags' => 'array'
    ];

    /**
     * Scope queries to articles that have been published
     * 
     * @param  $query
     */
    public function scopeIsExist($query, $taskId)
    {
        $query->where('is_exist', '=', true)->where('pos_member_import_task_id', '=', $taskId);
    }

    /**
     * Scope queries to articles that have been published
     * 
     * @param  $query
     */
    public function scopeIsNotExist($query, $taskId)
    {
        $query->where('is_exist', '=', false)->where('pos_member_import_task_id', '=', $taskId);
    }

    /**
     * 32 = 100000
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function scopeIsNotExecuted($query)
    {
        $query->where(DB::raw('32&Status'), '!=', 32);
    }

    /**
     * @param  [type] $query [description]
     * @return [type]        [description]
     */
    public function scopeIsExecuted($query)
    {
        $query->where(DB::raw('32&Status'), '=', 32);
    }

    public function scopeIsBelong($query, $taskId)
    {
        $query->where('pos_member_import_task_id', '=', $taskId);
    }

    public function scopeIsDuplicate($query, $colName = 'id')
    {
        $query               
            ->whereNotNull($colName)
            ->where($colName, '<>', '')
            ->groupBy(['name', $colName])
            ->orderBy('id', 'DESC')
            ->having(DB::raw('COUNT(*)'), '>', 1)
        ;
    }

    public function scopeDuplicateWithThis($query, $colName, $duplicateContent)
    {
        $query
            ->where('name', '=', $duplicateContent->name)
            ->where($colName, '=', $duplicateContent->$colName)
            ->where($colName, '<>', '')
            ->skip(1)->take(20)
        ;
    }

    public function scopeNullColumnFilter($query, $columns)
    {
        $query->where(function ($q) use ($columns) {
            foreach ($columns as $key => $column) {
                $q->orWhereNull($key);
            }  
        });            
    }

    /**
     * An article is owned by a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pos_member_import_task()
    {
        return $this->belongsTo('App\Model\Flap\PosMemberImportActivityTask');
    }

    /**
     * An article is owned by a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state()
    {
        return $this->belongsTo('App\Model\State');
    }

    public function setIsExist($member)
    {
        $boolean = !empty($member);
        $this->is_exist = $boolean;

        $this->code = (true === $boolean) ? $member['Code'] : NULL;
        $this->serno = (true === $boolean) ? $member['SerNo'] : NULL;
        $this->sernoi = (true === $boolean) ? $member['MemberSerNoI'] : NULL;

        $desFlags = (true === $boolean) ? $this->pos_member_import_task->update_flags : $this->pos_member_import_task->insert_flags;
        $diffFlags = (true === $boolean) ? $this->pos_member_import_task->insert_flags : $this->pos_member_import_task->update_flags;

        if (!is_array($this->flags)) {
            $this->flags = [];
        }

        $this->flags = array_diff($this->flags, $diffFlags);
        $this->flags = array_merge($this->flags, $desFlags);
        
        return $this;
    }

    public function getFlagVal()
    {
        $flags = $this->flags;

        foreach (json_decode(Import::TARGET_FLAGS) as $target) {
            if (!array_key_exists(Flater::genKey($target), $flags)) {
                continue;
            }

            $targetFlag = array_get($flags, Flater::genKey($target), Import::DEFAULT_FLAG_VALUE);
                
            if (false !== array_search($targetFlag, [Import::DEFAULT_FLAG_VALUE])) {
                continue;
            }

            return $targetFlag;
        }

        return Import::DEFAULT_FLAG_VALUE;
    }

    public function genMemo()
    {
        return;
    }

    public function getZipcode()
    {
        return (NULL === $this->state) ? Import::DEFAULT_ZIPCODE : $this->state->zipcode;
    }

    public function getCityName()
    {
        return (NULL === $this->state) ? Import::DEFAULT_CITYSTATE : $this->state->city->name;
    }

    public function getStateName()
    {
        return (NULL === $this->state) ? Import::DEFAULT_CITYSTATE : $this->state->name;
    }
    public function fixStatus()
    {
        $this->status = with(new StatusHandler(new StatusRequest($this)))->getRequest()->getStatus();

        return $this;
    }

    public function getOpacity()
    {
        $status = ($this->status&bindec('111011111'));
        $index = 8;

        for ($i = 0; $i < strlen(decbin($status)); $i ++) {
            if ('1' == decbin($status)[$i]) {
                $index --;
            }
        }

        return 0 >= $index ? 0 : (1 * $index)/8;
    }

    public function getFlags()
    {
        $flags = $this->getFlagPrototype();

        $flags = empty($this->serno) 
            ? array_merge($flags, $this->pos_member_import_task->insert_flags) 
            : $this->pos_member_import_task->update_flags
        ;

        return $flags;
    }

    public function getFlagPrototype()
    {
        $flags = [];

        for ($i = 1; $i <= 40; $i ++) {
            $flags[Flater::genKey($i)] = Import::DEFAULT_FLAG_VALUE;
        }

        return $flags;
    }
}
