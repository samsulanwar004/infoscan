<?php

use Illuminate\Database\Seeder;

class CreateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $u = new \App\User;
        $u->email = 'pieter@rebelworks.co';
        $u->name = 'Pieter Lelaona';
        $u->password = bcrypt('masuk123');
        $u->is_active = true;
        $u->save();
    }
}
