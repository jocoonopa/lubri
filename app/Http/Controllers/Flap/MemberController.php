<?php

namespace App\Http\Controllers\Flap;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\Helper\Flap\PosMemberProfileHelper;
use App\Utility\Chinghwa\Helper\Flap\PosMemberListHelper;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = with(new PosMemberListHelper)->get(Auth::user());

        return ('mix' === $request->query->get('type')) 
            ? view('flap.members.indexMix', compact('members'))
            : view('flap.members.indexTable', compact('members'))
        ;
    }

    public function show($code)
    {
        return view('flap.members.show', compact('code'));
    }
}
