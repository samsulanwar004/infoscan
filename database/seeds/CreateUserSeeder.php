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
        'Points.List','Points.Create','Points.Update','Points.Delete',
        'Member.List','Member.Show',
        'Snaps.List','Snaps.Show',        
        'Settings.List',
        'Transactions.List',
        'Reports.List',
    ];

    protected $createPermissionsCrowdsourceAdmin = [
        'User.List','User.Create','User.Update','User.Delete',
        'Brand.List','Brand.Create','Brand.Update','Brand.Delete',
        'Exchange.List','Exchange.Create','Exchange.Update','Exchange.Delete',
        'Promotion.List','Promotion.Create','Promotion.Update','Promotion.Delete',
        'LuckyDraw.List','LuckyDraw.Create','LuckyDraw.Update','LuckyDraw.Delete',
        'Questionnaire.List','Questionnaire.Create','Questionnaire.Update','Questionnaire.Delete',
        'Questions.List','Questions.Create','Questions.Update','Questions.Delete',
        'Ses.List','Ses.Create','Ses.Update','Ses.Delete',
        'Points.List','Points.Create','Points.Update','Points.Delete',
        'Member.List','Member.Show',
        'Snaps.List','Snaps.Show',        
        'Settings.List',
        'Transactions.List',
        'Reports.List',
    ];

    protected $createPermissionsCrowdsourceSupervisor = [
        'Brand.List','Brand.Create','Brand.Update','Brand.Delete',
        'Exchange.List','Exchange.Create','Exchange.Update','Exchange.Delete',
        'Promotion.List','Promotion.Create','Promotion.Update','Promotion.Delete',
        'LuckyDraw.List','LuckyDraw.Create','LuckyDraw.Update','LuckyDraw.Delete',
        'Questionnaire.List','Questionnaire.Create','Questionnaire.Update','Questionnaire.Delete',
        'Questions.List','Questions.Create','Questions.Update','Questions.Delete',
        'Ses.List','Ses.Create','Ses.Update','Ses.Delete',
        'Points.List','Points.Create','Points.Update','Points.Delete',
        'Member.List','Member.Show',
        'Snaps.List','Snaps.Show',
        'Transactions.List',
        'Reports.List',
    ];

    protected $createPermissionsCrowdsourceAccount = [
        'Snaps.List','Snaps.Show',
    ];

    protected $createPermissionsVendorAdmin = [
        'Merchant.List','Merchant.Create','Merchant.Update','Merchant.Delete',
        'Brand.List','Brand.Create','Brand.Update','Brand.Delete',
        'Promotion.List','Promotion.Create','Promotion.Update','Promotion.Delete',
    ];

    protected $createPermissionsVendorAccount = [
        'Brand.List','Brand.Create','Brand.Update','Brand.Delete',
        'Promotion.List','Promotion.Create','Promotion.Update','Promotion.Delete',
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

        $ca = new \Rebel\Component\Rbac\Models\Role();
        $ca->role_name = 'Crowdsource Admin';
        $ca->role_label = 'Crowdsource Admin';
        $ca->is_active = 1;
        $ca->save();

        foreach ($this->createPermissionsCrowdsourceAdmin as $permission) {
            // add permission for current role {Crowdsource Admin}            
            $ca->addPermissions($permission);
        }

        $cs = new \Rebel\Component\Rbac\Models\Role();
        $cs->role_name = 'Crowdsource Supervisor';
        $cs->role_label = 'Crowdsource Supervisor';
        $cs->is_active = 1;
        $cs->save();

        foreach ($this->createPermissionsCrowdsourceSupervisor as $permission) {
            // add permission for current role {Crowdsource Supervisor}            
            $cs->addPermissions($permission);
        }

        $cac = new \Rebel\Component\Rbac\Models\Role();
        $cac->role_name = 'Crowdsource Account';
        $cac->role_label = 'Crowdsource Account';
        $cac->is_active = 1;
        $cac->save();

        foreach ($this->createPermissionsCrowdsourceAccount as $permission) {
            // add permission for current role {Crowdsource Account}            
            $cac->addPermissions($permission);
        }

        $va = new \Rebel\Component\Rbac\Models\Role();
        $va->role_name = 'Vendor Admin';
        $va->role_label = 'Vendor Admin';
        $va->is_active = 1;
        $va->save();

        foreach ($this->createPermissionsVendorAdmin as $permission) {
            // add permission for current role {Vendor Admin}            
            $va->addPermissions($permission);
        }

        $vac = new \Rebel\Component\Rbac\Models\Role();
        $vac->role_name = 'Vendor Account';
        $vac->role_label = 'Vendor Account';
        $vac->is_active = 1;
        $vac->save();

        foreach ($this->createPermissionsVendorAccount as $permission) {
            // add permission for current role {Vendor Account}            
            $vac->addPermissions($permission);
        }

    }
}
