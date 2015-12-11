<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;

use App\Model\User;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        Session::flash('success', "您已經新增了使用者<b>{$user->username}</b>");

        return redirect('user');
    }

    /**
     * Display the specified resource.
     *
     * @param  App\Model\User $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user){}

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
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Model\User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $user->update($request->all());

        Session::flash('success', "您已經更新了<b>{$user->username}</b>的資料");

        return redirect("user/{$id}/edit");
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

        Session::flash('success', "您已經移除了<b>{$user->username}</b>");

        return redirect('user');
    }
}
