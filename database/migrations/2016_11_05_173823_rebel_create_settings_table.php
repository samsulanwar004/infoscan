<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RebelCreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $t) {
            $t->increments('id');
            $t->string('setting_group', 200)->index();
            $t->string('setting_name', 200)->nullable()->index();
            $t->string('setting_display_name', 200)->index();
            $t->text('setting_value');
            $t->string('setting_type', 50)->default('str');
            $t->boolean('is_visible')->default(false);
            $t->string('created_by', 100)->nullable();
            $t->string('updated_by', 100)->nullable();
            $t->timestamps();
            $t->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('settings');
    }
}
