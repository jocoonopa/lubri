<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

class ITAuthenticate
{
    const ALLOW_CORP = '資訊部';

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (self::ALLOW_CORP !== $this->auth->user()->corp) {
           return response()->view('errors.403', ['title' => '您不是資訊部人員，沒有足夠權限瀏覽此頁面'], 403);
        }

        return $next($request);
    }
}
