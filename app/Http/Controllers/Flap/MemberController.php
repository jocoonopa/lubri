<?php

namespace App\Http\Controllers\Flap;

use Auth;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Utility\Chinghwa\Helper\PosMemberListHelper;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index()
    {
        $listHelper = new PosMemberListHelper;

        $user = Auth::user();

        $members = $listHelper->get($user);

        return view('flap.members.index', compact('members'));
    }

    public function show()
    {
        return 'show';
    }

    public function detail()
    {
        return 'detail';
    }
}
