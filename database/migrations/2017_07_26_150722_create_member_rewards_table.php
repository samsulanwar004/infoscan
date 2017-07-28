<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_rewards', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('member_id');
            $table->string('trx_no');
            $table->dateTime('trx_time');
            $table->string('order_number', 50);
            $table->string('brand', 50);
            $table->string('egift_code', 50);
            $table->string('program_name', 255);
            $table->string('item_name', 255)->nullable();
            $table->double('value');
            $table->dateTime('expired_date');
            $table->string('url', 255)->nullable();
            $table->string('image', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_rewards');
    }
}
