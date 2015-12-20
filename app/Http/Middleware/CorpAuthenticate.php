<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;

class CorpAuthenticate
{
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
        $corps = $this->getCorp($request);

        if (false === array_search($this->auth->user()->corp, $corps)) {
           return response()->view('errors.403', ['title' => '您不是<strong>' . implode($corps, ',') . '</strong>人員，沒有足夠權限瀏覽此頁面'], 403);
        }

        return $next($request);
    }

    protected function getCorp($request)
    {
        $corps = [];
        $actions = $request->route()->getAction();

        if (is_array($actions['corp'])) {
            return array_merge($corps, $actions['corp']);
        }

        $corps[] = $actions['corp'];

        return $corps;
    }
}
