<?php

namespace Rebel\Component\Rbac\Models;

use Illuminate\Database\Eloquent\Model;
use Rebel\Component\Rbac\Contracts\Role as RoleContract;
use Rebel\Component\Rbac\Exceptions\PermissionNotFoundException;

class Role extends Model implements RoleContract
{
    use \Rebel\Component\Rbac\Traits\CacheRefresher;

    /**
     * @var  string
     */
    private $permissionModel;

    public function __construct(array $attributes = [])
    {
        $this->permissionModel = config('rebel-rbac.permissions.model', \Rebel\Component\Rbac\Models\Permission::class);
        parent::__construct();
    }

    protected static function boot()
    {
        parent::boot();

        static::bootCacheRefresher();
    }

    /**
     * @inheritdoc
     */
    public function permissions()
    {
        return $this->belongsToMany(
            $this->permissionModel,
            config('rebel-rbac.role_permission_pivot_table', 'roles_permissions')
        );
    }

    /**
     * @inheritdoc
     */
    public function users()
    {
        return $this->belongsToMany(
            config('auth.providers.users.model', \App\User::class),
            config('rebel-rbac.user_role_pivot_table', 'users_roles')
        );
    }

    /**
     * @inheritdoc
     */
    public static function getByName($name)
    {
        $role = static::where('role_name', '=', $name)->first();
        if(! $role) {
            throw new \Rebel\Component\Rbac\Exceptions\RoleNotFoundException();
        }

        return $role;
    }

    /**
     * @inheritdoc
     */
    public function hasPermissions()
    {
        return $this->permissionCount() > 0;
    }

    /**
     * @inheritdoc
     */
    public function permissionCount()
    {
        return $this->permissions->count();
    }

    /**
     * @inheritdoc
     */
    public function addPermissions($permissions)
    {
        // if string
        if(is_string($permissions)) {
            $pModel = $this->permissionModel;
            $model = new $pModel;
            $p =$model->where('permission_name', '=', $permissions)->first();

            if(! $p) {
                throw new PermissionNotFoundException('There is no permissions with name: ' . $permissions);
            }

            return $this->permissions()->save($p);
        }

        // if instance of model
        if($permissions instanceof \Rebel\Component\Rbac\Contracts\Permission) {
            return $this->permissions()->save($permissions);
        }

        // if array
        if(is_array($permissions)) {
            return $this->permissions()->sync($permissions);
        }

        throw new \InvalidArgumentException("Invalid value type in 1st argument [$permissions].");
    }
}