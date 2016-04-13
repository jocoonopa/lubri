<?php

namespace App\Http\Controllers\Flap\POS_Member;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Model\Flap\PosMemberImportKind;
use Illuminate\Http\Request;

class ImportKindController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('flap.posmember.import_kind.index', ['kinds' => PosMemberImportKind::all()]);
    }
}
