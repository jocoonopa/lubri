<?php

namespace App\Http\Controllers\Intro;

use App\Http\Controllers\Controller;

class IntroController extends Controller
{
    public function report()
    {
        return view('intro.list', [
            'title' => '報表一覽'
        ]);
    }

    public function b()
    {
        return view('intro.b', [
        	'title' => '廠商一覽'
        ]);
    }
}