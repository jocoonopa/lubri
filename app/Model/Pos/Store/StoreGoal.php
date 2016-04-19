<?php

namespace App\Model\Pos\Store;

use App\Model\Pos\Store\Store;
use Illuminate\Database\Eloquent\Model;

class StoreGoal extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stores_goals';

    protected $attributes = [
        'origin_goal'    => 0,
        'pl_origin_goal' => 0,
        'new_goal'       => 0,
        'pl_new_goal'    => 0
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'user_id', 'store_id', 'new_goal', 'origin_goal', 'pl_origin_goal', 'pl_new_goal', 'start_at', 'stop_at'];

    protected $dates = ['start_at', 'stop_at'];

    public function store()
    {
        return $this->belongsTo('App\Model\Pos\Store\Store');
    }

    public function scopeFindByYear($q, $y)
    {
        return $q->where('year', '=', $y);
    }

    public function scopeFindByMonth($q, $m)
    {
        return $q->where('month', '=', $m);
    }

    public function scopeFindByStore($q, Store $store)
    {
        return $q->where('store_id', '=', $store->id);
    }
}
