<?php

return [
    /*
    |--------------------------------------------------------------------------
    | User - Role Pivot Table Name
    |--------------------------------------------------------------------------
    |
    | Make user role pivot table name configurable
    */
    'user_role_pivot_table' => 'users_roles',

    /*
    |--------------------------------------------------------------------------
    | Role - Permission Pivot Table Name
    |--------------------------------------------------------------------------
    |
    | Make role permission pivot table name configurable
    */
    'role_permission_pivot_table' => 'roles_permissions',

    /*
    |--------------------------------------------------------------------------
    | Role Configuration
    |--------------------------------------------------------------------------
    |
    | You can change the role model and table name.
    */
    'roles' => [
        'table' => 'roles',
        'model' => \Rebel\Component\Rbac\Models\Role::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Permission Configuration
    |--------------------------------------------------------------------------
    |
    | You can change the permission model and table name.
    */
    'permissions' => [
        'table' => 'permissions',
        'model' => \Rebel\Component\Rbac\Models\Permission::class,
    ],
];