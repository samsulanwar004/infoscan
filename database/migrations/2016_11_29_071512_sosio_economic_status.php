<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SosioEconomicStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('socio_economic_status', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 2)->unique();
            $table->decimal('range_start', 15, 5)->default(0);
            $table->decimal('range_end', 15, 5)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('socio_economic_status');
    }
}
