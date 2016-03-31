<?php

namespace App\Model\Flap;

use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Flap\CCS_MemberFlags\Flater;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class AgentSaleRecord extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'agent_sale_record';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'target', 'record', 'month'];
}
