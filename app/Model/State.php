<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'state';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'pastname',
        'zipcode'
    ];

    public $timestamps = false;

    public function city()
    {
        return $this->belongsTo('App\Model\City');
    }

    public function isBelong($address)
    {
        return false !== strpos($address, $this->name) || false !== strpos($address, $this->pastname);
    }

    public function scopeFindByZipcode($query, $zipcode)
    {
        $query->where('zipcode', '=', $zipcode);
    }
}
