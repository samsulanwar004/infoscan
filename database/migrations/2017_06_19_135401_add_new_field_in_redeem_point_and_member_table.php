<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNewFieldInRedeemPointAndMemberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('redeem_point', function (Blueprint $t) {
            $t->double('current_point')->nullable();
            $t->double('current_cash')->nullable();
        });

        Schema::table('members', function (Blueprint $t) {
            $t->string('referral_me', 100)->nullable();
            $t->double('temporary_cash')->nullable();
        });
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
