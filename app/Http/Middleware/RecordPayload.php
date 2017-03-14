<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;

class RecordPayload
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
        if('get' !== strtolower($request->method())) {
            DB::insert('Insert into mobile_request_logs (rc, method, type, content, created_at) values(?,?,?,?,?)', [
                str_random(10), $request->method(), 'request', $request->fullUrl() . "\n" . json_encode($request->all()), date('Y-m-d H:i:s')
            ]);
        }

        return $next($request);
    }
}
