<?php

namespace App\Model\Pos\Store;

use Illuminate\Database\Eloquent\Model;

class StoreArea extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stores_areas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'name', 'store_id'];
}
