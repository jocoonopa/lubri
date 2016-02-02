<?php

namespace App\Model\Flap;

use Illuminate\Database\Eloquent\Model;

class PosMemberImportTaskContent extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'posmember_import_task_content';

    public $timestamps = false;

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
        'zipcode',
        'city',
        'state',
        'homeaddress',
        'birthday',
        'member_class_serno',
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
        'is_exist'
    ];

    /**
     * Scope queries to articles that have been published
     * 
     * @param  $query
     */
    public function scopeIsExist($query, $taskId)
    {
        $query->where('is_exist', '=', true)->where('posmember_import_task_id', '=', $taskId);
    }

    /**
     * Scope queries to articles that have been published
     * 
     * @param  $query
     */
    public function scopeIsNotExist($query, $taskId)
    {
        $query->where('is_exist', '=', false)->where('posmember_import_task_id', '=', $taskId);
    }
}
