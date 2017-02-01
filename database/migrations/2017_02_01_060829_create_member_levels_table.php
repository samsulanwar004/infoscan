<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_levels', function (Blueprint $t) {
            $t->increments('id');
            $t->integer('member_id')->unsigned();
            $t->unsignedInteger('level_id');
            $t->float('latest_point');            
            $t->float('latest_level_point');            
            $t->timestamps();

            $t->foreign('member_id')->references('id')->on('members');
            $t->foreign('level_id')->references('id')->on('level_points');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_levels');
    }
}
