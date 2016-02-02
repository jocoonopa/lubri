<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'city';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'pastname',
        'telcode'
    ];

    public $timestamps = false;

    /**
     * A user can have many articles
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function states()
    {
        return $this->hasMany('App\Model\State');
    }
}
