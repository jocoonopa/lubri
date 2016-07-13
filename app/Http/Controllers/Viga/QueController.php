<?php

namespace App\Http\Controllers\Viga;

use App\Http\Controllers\Controller;
use App\Model\Log\FVSyncQue;

class QueController extends Controller
{
    public function index()
    {
        return view('viga.que.index', ['ques' => FVSyncQue::latest()->paginate(10)]);
    }
}
