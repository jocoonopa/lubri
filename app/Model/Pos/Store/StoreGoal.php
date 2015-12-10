<?php

namespace App\Model\Pos\Store;

use Illuminate\Database\Eloquent\Model;

class StoreGoal extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stores_goals';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'store_id', 'new_goal', 'origin_goal', 'pl_origin_goal', 'pl_new_goal', 'start_at', 'stop_at'];

    protected $dates = ['start_at', 'stop_at'];
}
