<?php

namespace App\Model\Pos\Store;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stores';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'sn', 'store_area_id', 'is_active'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * A user can have many articles
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function goals()
    {
        return $this->hasMany('App\Model\Pos\Store\StoreGoal');
    }

    public function storeArea()
    {
        return $this->belongsTo('App\Model\Pos\Store\StoreArea');
    }

    public function scopeFindActive($q)
    {
        return $q->where('is_active', '=', true);
    }
}
