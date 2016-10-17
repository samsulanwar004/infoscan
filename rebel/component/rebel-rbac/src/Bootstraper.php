<?php

namespace Rebel\Component\Rbac;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Rebel\Component\Rbac\Contracts\Permission as PermissionContract;

class Bootstraper
{
    protected $gate;

    protected $cache;

    protected $cacheKey = '_rebel.component.rbac';

    public function __construct(GateContract $gate, CacheRepository $cache)
    {
        $this->gate = $gate;

        $this->cache = $cache;
    }

    public function registerAbilities()
    {
        $permissions = $this->getPermissions();
        foreach ($permissions as $permission) {
            $this->gate->define($permission->permission_name, function($user) use($permission) {
                return $user->hasRole($permission->roles);
            });
        }

        return $this->gate;
    }

    public function forgetCachedPermissions()
    {
        $this->cache->forget($this->cacheKey);

        return $this;
    }


    protected function getPermissions()
    {
        if($result = $this->cache->get($this->cacheKey)) {
            return $result;
        }

        return $this->cache->rememberForever($this->cacheKey, function() {
            return app(PermissionContract::class)->with('roles')->get();
        });
    }
}