<?php

namespace App\Http\Middleware;

use App\Model\Flap\PosMemberImportKind;
use Closure;
use Illuminate\Auth\Guard;
use Session;

class ImportKindMiddleware
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
        $importKind = $request->import_task ? $request->import_task->kind()->first() : PosMemberImportKind::find($request->get('kind_id'));

        if (!$importKind || !$importKind->is_enabled) {
            return response()->view('errors.404', [], 404);
        }

        if (!in_array($this->auth->user()->corp, $importKind->allow_corps)) {
            return response()->view('errors.403', [], 403);
        }

        return $next($request);
    }
}
