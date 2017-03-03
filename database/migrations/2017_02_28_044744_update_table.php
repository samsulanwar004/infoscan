<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $t) {
            $t->unsignedInteger('snap_id')->nullable();
        });

        Schema::table('members_lucky_draws', function (Blueprint $t) {
            $t->string('random_number', 7);
        });

        /*Schema::table('promotions', function (Blueprint $t) {
            $t->double('point')->default(0.00);
        });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
