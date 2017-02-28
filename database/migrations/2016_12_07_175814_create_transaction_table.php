<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $t) {
            $t->increments('id');
            $t->string('transaction_code', 20)->unique();
            $t->string('member_code', 10)->index();
            $t->unsignedInteger('transaction_type')->index(); // [1=>'add_point_to_member', 2=>'redeem_point_to_cash']
            $t->timestamps();

        });

        Schema::create('transaction_detail', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('transaction_id');
            $t->string('member_code_from', 10)->index();
            $t->string('member_code_to', 10)->index();
            $t->double('amount', 15, 2)->default(0);
            $t->string('detail_type', 5)->index(); // point, cash
            $t->timestamps();

            $t->foreign('transaction_id', 'FK_transaction_id_in_transaction_details')
              ->references('id')->on('transactions')
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
        Schema::dropIfExists('transaction_detail');
        Schema::dropIfExists('transactions');
    }
}
