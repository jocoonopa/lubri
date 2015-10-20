<?php

namespace App\Http\Controllers\Broad;

use App\Http\Controllers\Controller;

class TodoController extends Controller
{
    public function index()
    {
        return view('basic.simple', [
            'title' => 'Todo', 
            'des' => '<p>1. 景華員工資料庫建立</p><p>2. 權限控管</p><p>3. 更多報表</p><p>4. Please tell me ~</p>',
            'res' => NULL       
        ]);
    }
}