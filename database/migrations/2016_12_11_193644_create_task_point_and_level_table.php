<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskPointAndLevelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $t) {
            $t->increments('id');
            $t->string('name', 100)->unique();
            $t->boolean('is_active')->default(1);
            $t->timestamps();
        });

        Schema::create('level_points', function (Blueprint $t) {
            $t->increments('id');
            $t->string('name', 10)->unique();
            $t->timestamps();
        });

        Schema::create('tasks_level_points', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('task_id');
            $t->unsignedInteger('level_id');
            $t->float('point');
            $t->timestamps();

            $t->foreign('task_id', 'FK_task_id_in_tasks_level_points')
              ->references('id')->on('tasks');

            $t->foreign('level_id', 'FK_level_id_in_tasks_level_points')
              ->references('id')->on('level_points');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks_level_points');
        Schema::dropIfExists('level_points');
        Schema::dropIfExists('tasks');
    }
}
