<?php

use Illuminate\Database\Seeder;

class CreateUserSeeder extends Seeder
{

    protected $createPermissions = [
        'User.List','User.Create','User.Update','User.Delete',
        'Role.List','Role.Create','Role.Update','Role.Delete',
        'Permission.Create','Permission.Update',
        'Merchant.List','Merchant.Create','Merchant.Update','Merchant.Delete',
        'Brand.List','Brand.Create','Brand.Update','Brand.Delete',
        'Exchange.List','Exchange.Create','Exchange.Update','Exchange.Delete',
        'Promotion.List','Promotion.Create','Promotion.Update','Promotion.Delete',
        'LuckyDraw.List','LuckyDraw.Create','LuckyDraw.Update','LuckyDraw.Delete',
        'Questionnaire.List','Questionnaire.Create','Questionnaire.Update','Questionnaire.Delete',
        'Questions.List','Questions.Create','Questions.Update','Questions.Delete',
        'Ses.List','Ses.Create','Ses.Update','Ses.Delete',
        'Point.List','Settings.List','Snaps.List','Transactions.List',
    ];

    protected $createPermissionsMerchant = [
        'MerchantUser.List','MerchantUser.Create','MerchantUser.Update','MerchantUser.Delete',
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

        // create merchant role
        $m = new \Rebel\Component\Rbac\Models\Role();
        $m->role_name = 'Merchant User';
        $m->role_label = 'Merchant User';
        $m->is_active = 1;
        $m->save();

        foreach ($this->createPermissionsMerchant as $permission) {
            $p = new \Rebel\Component\Rbac\Models\Permission();
            $p->permission_name = $permission;
            $p->permission_label = $permission;
            $p->permission_group = 'users';
            $p->save();

            // add permission for current role {merchant user}
            $m->addPermissions($p);
        }

    }
}
