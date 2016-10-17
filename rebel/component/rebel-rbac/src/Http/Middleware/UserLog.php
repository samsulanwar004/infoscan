<?php

namespace Rebel\Component\Rbac\Http\Middleware;

use Closure;
use Illuminate\Database\DatabaseManager;

/**
 * User Logger Class
 */
class UserLog
{
    private $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}