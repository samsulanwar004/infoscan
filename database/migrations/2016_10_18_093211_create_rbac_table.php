<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRbacTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $t) {
            $t->increments('id');
            $t->string('role_name', 100)->unique();
            $t->string('role_label');
            $t->boolean('is_active')->default(true);
            $t->softDeletes();
            $t->timestamps();
        });

        Schema::create('permissions', function (Blueprint $t) {
            $t->increments('id');
            $t->string('permission_name', 100)->unique();
            $t->string('permission_label', 100);
            $t->string('permission_group', 100)->default('-');
            $t->softDeletes();
            $t->timestamps();
        });

        // create relation
        Schema::create('roles_permissions', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('role_id')->unsigned();
            $t->integer('permission_id')->unsigned();

            $t->foreign('role_id', 'fk_role_id_in_roles_permissions')
              ->references('id')
              ->on('roles')
              ->onDelete('CASCADE');
            $t->foreign('permission_id', 'fk_permission_id_in_roles_permissions')
              ->references('id')
              ->on('permissions')
              ->onDelete('CASCADE');
        });

        Schema::create('users_roles', function(Blueprint $t) {
            $t->increments('id');
            $t->integer('user_id')->unsigned();
            $t->integer('role_id')->unsigned();


            $t->foreign('user_id', 'fk_user_id_in_users_roles')
              ->references('id')
              ->on('users')
              ->onDelete('CASCADE');

            $t->foreign('role_id', 'fk_role_id_in_users_roles')
              ->references('id')
              ->on('roles')
              ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('roles_permissions');
        Schema::drop('users_roles');
        Schema::drop('permissions');
        Schema::drop('roles');
    }
}
