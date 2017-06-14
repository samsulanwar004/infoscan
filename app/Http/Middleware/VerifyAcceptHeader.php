<?php

namespace App\Http\Middleware;

use Closure;

class VerifyAcceptHeader
{
	public function handle($request, Closure $next, $guard = null)
	{
		if($request->hasHeader('accept') && !$request->wantsJson()) {
			return response('Forbidden', 403);
    	}

		return $next($request);
	}
}
