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
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::create($request->all());

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
