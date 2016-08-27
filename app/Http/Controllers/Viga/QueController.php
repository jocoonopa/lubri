<?php

namespace App\Http\Controllers\Viga;

use App\Http\Controllers\Controller;
use App\Model\Log\FVSyncQue;

class QueController extends Controller
{
    public function index()
    {
        return view('viga.que.index', [
            'ques'  => FVSyncQue::latest()->paginate(env('QUE_COUNT_PERPAGE', 100)),
            'limit' => env('QUE_COUNT_PERPAGE', 100)
        ]);
    }

    public function show(FVSyncQue $que)
    {
        pr($que->getAttributes());
        pr($que->conditions);
    }
}
