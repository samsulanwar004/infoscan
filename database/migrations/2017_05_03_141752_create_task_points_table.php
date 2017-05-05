<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task_points', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('task_id');
            $table->float('range_start')->nullable();
            $table->float('range_end')->nullable();
            $table->float('point')->nullable(); 
            $table->timestamps();

            $table->foreign('task_id', 'FK_task_id_in_task_points')
              ->references('id')->on('tasks');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('task_points');
    }
}
