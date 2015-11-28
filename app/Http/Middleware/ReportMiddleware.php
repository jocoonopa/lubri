<?php

namespace App\Http\Middleware;

use Closure;
use App\Utility\Chinghwa\ExportExcel;

class ReportMiddleware
{
    const TOKEN = 'Jocoonopa1234';
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (self::TOKEN !== $request->get('token') && ExportExcel::TOKEN !== $request->get('token')) {
            throw new \Exception('Token unvalid!');
        }

        return $next($request);
    }
}
