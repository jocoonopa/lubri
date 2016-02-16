<?php

namespace App\Http\Middleware;

use Closure;
use Session;

class ImportContentMiddleware
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
        $task = $request->imoprt_task;
        $content = $request->import_content;

        if (NULL === $content) {
           return response()->view('errors.404', [], 404);
        }
        
        if (32 === ($content->status&32)) {
            Session::flash('error', "由於同步已經完成，此項目無法修改或刪除!");

            return redirect()->url("/flap/pos_member/{$task->id}/content"); 
        }

        return $next($request);
    }
}
