<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromoPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promo_points', function (Blueprint $t) {
            $t->increments('id');
            $t->string('city_name', 100)->unique();
            $t->float('point_city');
            $t->dateTime('start_at');
            $t->dateTime('end_at');
            $t->boolean('is_active')->default(1);
            $t->timestamps();
        });

        Schema::create('promo_level_points', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('promo_point_id');
            $t->unsignedInteger('level_id');
            $t->float('point');
            $t->timestamps();

            $t->foreign('promo_point_id', 'FK_promo_point_id_in_promo_level_points')
              ->references('id')->on('promo_points');

            $t->foreign('level_id', 'FK_level_id_in_promo_level_points')
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
        Schema::dropIfExists('promo_level_points');
        Schema::dropIfExists('promo_points');
    }
}
