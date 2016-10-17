<?php

namespace Rebel\Component\Rbac\Traits;

trait HasRoles
{
    public function roles()
    {
        return $this->belongsToMany(
            config('rebel-rbac.roles.model', \Rebel\Component\Rbac\Models\Role::class),
            config('rebel-rbac.user_role_pivot_table', 'users_roles')
        );
    }

    /**
     * Match the roles and permissions.
     *
     * @param  string|Object|array  $roles
     * @return boolean
     */
    public function hasRole($roles)
    {
        if(is_string($roles)) {
            return $this->roles->contains('role_name', $roles);
        }

        if($roles instanceof  \Rebel\Component\Rbac\Contracts\Role) {
            $this->roles->contains('id', $roles->id);
        }

        // <TODO:> add validation for array value type.
        return (bool)$roles->intersect($this->roles)->count();
    }

    public function assignRole($role)
    {
        if (is_string($role)) {
            $roleModel = config('rebel-rbac.roles.model');
            $roleModel = new $roleModel;

            return $this->roles()->save(
                $roleModel->where('role_name', '=', $role)->first()
            );
        }

        if($role instanceof \Illuminate\Database\Eloquent\Model) {
            return $this->roles()->save($role);
        }

        if(is_array($role)) {
            return $this->roles()->sync($role);
        }

        throw new \InvalidArgumentException("Invalid value type in 1st argument [$role].");
    }
}