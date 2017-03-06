<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRedeemPointTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redeem_point', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('member_id');
            $table->double('point');
            $table->double('cashout');
            $table->string('bank_account');
            $table->string('account_number');
            $table->string('approved_by')->nullable();
            $table->timestamps();

            $table->foreign('member_id', 'FK_member_id_in_redeem_point')
              ->references('id')->on('member')
              ->onUpdate('NO ACTION')
              ->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('redeem_point');
    }
}
