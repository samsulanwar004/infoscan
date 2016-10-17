<?php

namespace Rebel\Component\Rbac\Models;

use Illuminate\Database\Eloquent\Model;
use Rebel\Component\Rbac\Contracts\Permission as PermissionContract;

class Permission extends Model implements PermissionContract
{
    use \Rebel\Component\Rbac\Traits\CacheRefresher;

    protected static function boot()
    {
        parent::boot();

        static::bootCacheRefresher();
    }

    /**
     * @inheritdoc
     */
    public function roles()
    {
        return $this->belongsToMany(
            config('rebel-rbac.roles.model', \Rebel\Component\Rbac\Models\Role::class),
            config('rebel-rbac.role_permission_pivot_table', 'roles_permissions')
        );
    }

    /**
     * inheritdoc
     */
    public static function getByName($name)
    {
        return static::where('permission_name', '=', $name)->first();
    }

    public function scopeDistinctGroup($query)
    {
        return static::groupBy('permission_group')->distinct()->get();
    }
}