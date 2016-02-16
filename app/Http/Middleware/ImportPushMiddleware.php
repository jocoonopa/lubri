<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class ImportPushMiddleware
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
        $task = $request->import_task;

        if (NULL === $task) {
           return response()->view('errors.404', [], 404);
        }
        
        if (NULL !== $task->executed_at) {
            Session::flash('error', "任務{$task->id}無法執行、修改或是刪除，因為該任務已經於{$task->executed_at} 執行完畢!");

            return redirect()->action('Flap\POS_Member\ImportTaskController@index'); 
        }

        return $next($request);
    }
}
