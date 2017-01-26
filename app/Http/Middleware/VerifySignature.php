<?php

namespace App\Http\Middleware;

use Closure;

class VerifySignature
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
        if($request->header('X-Signature')) {
            if(! (new ValidateSignature($signature, $request->all()))) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid Signature'
                ], 400);
            }
        }
        return $next($request);
    }
}
