<?php

namespace Rebel\Component\Rbac\Contracts;

interface Role
{
    /**
     * Initialize relation with permission model.
     *
     * @return mixed
     */
    public function permissions();

    /**
     * Initialize relation with user model.
     *
     * @return mixed
     */
    public function users();

    /**
     * Get Role by name
     *
     * @param $name
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByName($name);

    /**
     * Check is current role has permissions.
     * @return boolean
     */
    public function hasPermissions();

    /**
     * Get total permissions of current role.
     * @return int
     */
    public function permissionCount();

    /**
     * Add permissions data.
     * @param \Illuminate\Database\Eloquent\Model|array|string
     */
    public function addPermissions($permissions);
}