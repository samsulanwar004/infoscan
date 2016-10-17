<?php

namespace Rebel\Component\Rbac\Contracts;

interface Permission
{
    /**
     * Initialize relation with roles.
     *
     * @return mixed
     */
    public function roles();

    /**
     * Get permission by given name {permission_name}.
     * @param $name
     *
     * @return mixed
     */
    public static function getByName($name);

    /**
     * Get the unique of permission group.
     *
     * @return \Illuminate\Database\Eloquent\Collection|mixed
     */
    public function scopeDistinctGroup($query);
}