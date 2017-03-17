<?php

namespace App\Http\Middleware;

use App\Services\NotificationService;
use Closure;

class DeviceTokenCatcher
{

    private $avalilableMethods = ['post', 'put'];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(in_array(strtolower($request->method()), $this->avalilableMethods)) {
            $token = $request->header('X-PLAYER', null);
            if(null !== $token) {
                (new NotificationService)->updateMemberDeviceToken($token);
            }
        }

        return $next($request);
    }
}
