<?php

namespace App\Model\Flap;

use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;
use DB;
use Illuminate\Database\Eloquent\Model;

class PosMemberImportTaskContent extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'posmember_import_task_content';

    public $timestamps = true;

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
        'cellphone',
        'hometel',
        'officetel',
        'birthday',
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
        'is_exist' => 'boolean'
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

    /**
     * An article is owned by a user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function pos_member_import_task()
    {
        return $this->belongsTo('App\Model\Flap\PosMemberImportTask');
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

        $this->flags = json_encode((object) array_diff((array) json_decode($this->flags), (array) json_decode($diffFlags)));
        $this->flags = json_encode((object) array_merge((array) json_decode($this->flags), (array) json_decode($desFlags)));
        
        return $this;
    }

    public function getFlagVal()
    {
        $flags = json_decode($this->flags, true);

        $targets = [11, 12, 37, 38];

        foreach ($targets as $target) {
            if (array_key_exists("_{$target}_" . '', $flags)) {
                $targetFlag = array_get($flags, "_{$target}_", 'N');
                
                if (false !== array_search($targetFlag, ['N', ''])) {
                    continue;
                }

                return $targetFlag;
            }
        }

        return 'N';
    }

    public function getPeriodAt()
    {
        return (is_object($this->period_at)) ? $this->period_at : new \DateTime($this->period_at);
    }

    public function genMemo()
    {
        return array_get(self::getMemberListFlagMap(), $this->getFlagVal(), '--') . ';' 
            . $this->name . ';' 
            . $this->cellphone . ';' 
            . $this->getCityName() . $this->getStateName() . $this->homeaddress . ';' 
            . "預產期:{$this->getPeriodAt()->format('Ym')}" . ';' 
            . "生產醫院:{$this->hospital}";
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

    /**
     * FLAG 23
     * 
     * @return array
     */
    public static function getPeriodFlagMap()
    {
        return [
            '201504' => 'B',
            '201505' => 'C',
            '201506' => 'D',
            '201507' => 'E',
            '201508' => 'F',
            '201509' => 'G',
            '201510' => 'H',
            '201511' => 'I',
            '201512' => 'J',
            '201601' => 'K',
            '201602' => 'L',
            '201603' => 'M',
            '201604' => 'O',
            '201605' => 'P',
            '201606' => 'Q',
            '201607' => 'R',
            '201608' => 'S',
            '201609' => 'T',
            '201610' => 'U',
            '201611' => 'V',
            '201612' => 'W',
        ];
    }

    public static function getMemberListFlagMap()
    {
        return [
            'A' => '1月上',
            'B' => '1月下',
            'C' => '2月上',
            'D' => '2月下',
            'E' => '3月上',
            'F' => '3月下',
            'G' => '4月上',
            'H' => '4月下',
            'I' => '5月上',
            'J' => '5月下',
            'K' => '6月上',
            'L' => '6月下',
            'M' => '7月上',
            'O' => '7月下',
            'P' => '8月上',
            'Q' => '8月下',
            'R' => '9月上',
            'S' => '9月下',
            'T' => '10月上',
            'U' => '10月下',
            'V' => '11月上',
            'W' => '11月下',
            'X' => '12月上',
            'Y' => '12月下'
        ];
    }
}
