<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberReferralsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_referrals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('member_id_referral')->unsigned();
            $table->integer('member_id_referrer')->unsigned();
            $table->double('referral_point');
            $table->double('referrer_point');
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
        Schema::dropIfExists('member_referrals');
    }
}
