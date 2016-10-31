<?php

use Illuminate\Database\Seeder;

class CreateUserSeeder extends Seeder
{

    protected $createPermissions = [
        'User.List',
        'User.Create',
        'User.Update',
        'User.Delete',
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $u = new \App\User;
        $u->email = 'admin@rebelworks.co';
        $u->name = 'Pieter Lelaona';
        $u->password = bcrypt('masuk123');
        $u->is_active = true;
        $u->save();

        $r = new \Rebel\Component\Rbac\Models\Role();
        $r->role_name = 'Super Administrator';
        $r->role_label = 'Super Administrator';
        $r->is_active = 1;
        $r->save();

        foreach ($this->createPermissions as $permission) {
            $p = new \Rebel\Component\Rbac\Models\Permission();
            $p->permission_name = $permission;
            $p->permission_label = $permission;
            $p->permission_group = 'users';
            $p->save();

            // add permission for current role {super administrator}
            $r->addPermissions($p);
        }


        // assign role for this user;
        $u->assignRole($r);
    }
}
