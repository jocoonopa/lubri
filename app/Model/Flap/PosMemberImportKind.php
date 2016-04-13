<?php

namespace App\Model\Flap;

use Illuminate\Database\Eloquent\Model;

class PosMemberImportKind extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'pos_member_import_kind';

    protected $casts = [
        'allow_corps' => 'array'
    ];
}
