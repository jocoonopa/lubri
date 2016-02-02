<?php

namespace App\Http\Controllers\Flap\POS_Member;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\Flap\POS_Member\ImportRequest;
use App\Utility\Chinghwa\Database\Connectors\Connector;
use App\Utility\Chinghwa\Database\Query\Processors\Processor;
use App\Utility\Chinghwa\Flap\POS_Member\Import\Import;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    public function index()
    {
        return view('flap.posmember.import.index', ['title' => '麗嬰房會員名單匯入']);
    }

    /**
     * The inject priority must be corrected, otherwise the validation will be failed
     *
     * @param  ImportRequest   $request
     * @param  PosMemberImport $import 
     * @return \Illuminate\Http\Response                  
     */
    public function process(ImportRequest $request, Import $import)
    {
        $import->handleImport();

        return __METHOD__;
    }
}
