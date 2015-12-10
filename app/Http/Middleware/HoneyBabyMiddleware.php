<?php

namespace App\Http\Middleware;

use Closure;
use App\Utility\Chinghwa\ExportExcel;
use App\Utility\Chinghwa\Compare\HoneyBaby;
use Validator;

class HoneyBabyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $validator = Validator::make($request->all(),  ['excel' => 'required|mimes:xlsx']);

        if ($validator->fails()) {
            return view('compare.honeybaby.index', [
                'title' => HoneyBaby::TITLE, 
                'res'   => ExportExcel::VALIDATE_INVALID_MSG]
            );
        }

        set_time_limit(0);
        ini_set('memory_limit', '512M');

        return $next($request);
    }
}
