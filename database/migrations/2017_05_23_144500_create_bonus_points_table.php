<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBonusPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bonus_points', function (Blueprint $table) {
            $table->increments('id');
            $table->string('bonus_name')->unique();
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });

        Schema::create('bonus_level_points', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('bonus_point_id');
            $t->unsignedInteger('level_id');
            $t->float('point');
            $t->timestamps();

            $t->foreign('bonus_point_id', 'FK_bonus_point_id_in_bonus_level_points')
              ->references('id')->on('bonus_points');

            $t->foreign('level_id', 'FK_level_id_in_bonus_level_points')
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
        Schema::dropIfExists('bonus_points');
        Schema::dropIfExists('bonus_level_points');
    }
}
