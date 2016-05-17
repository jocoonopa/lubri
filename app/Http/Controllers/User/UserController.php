<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Model\User;
use DB;
use Illuminate\Http\Request;
use Session;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('corp')->get();

        return view('user.index', ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = new User($request->all());
        $user->email = $user->account . env('DOMAIN');
        $user->password = bcrypt($user->password);
        $user->save();

        return $this->redirectWithSuccessFlash('user', "您已經新增了使用者<b>{$user->username}</b>");
    }

    /**
     * Display the specified resource.
     */
    public function show(){}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  App\Model\User $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('user.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * ps: Queue works when you have 3rd party service taking care for your jobs like Amazon SQS. 
     * Using Amazon SQS, you can queue mails, * jobs, etc. If 3rd party service is not configured, 
     * Laravel performs all command at the same time as a fail-safe.
     * 
     * @param  Illuminate\Http\Request $request
     * @param  App\Model\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->update($request->all());

        return $this->redirectWithSuccessFlash("user/{$user->id}/edit", "您已經更新了<b>{$user->username}</b>的資料");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\Model\User $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return $this->redirectWithSuccessFlash('user', "您已經移除了<b>{$user->username}</b>");
    }

    protected function redirectWithSuccessFlash($url, $msg)
    {
        Session::flash('success', $msg);

        return redirect($url);
    }
}
