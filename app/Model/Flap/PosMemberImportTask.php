<?php

namespace App\Model\Flap;

use Illuminate\Database\Eloquent\Model;

class PosMemberImportTask extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'posmember_import_task';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'executed_at',
        'update_count',
        'insert_count',
        'error_count'
    ];

    /**
     * A user can have many articles
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function content()
    {
        return $this->hasMany('App\Model\Flap\PosMemberImportTaskContent');
    }
}
