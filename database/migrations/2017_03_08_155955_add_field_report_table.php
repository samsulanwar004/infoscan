<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reports', function (Blueprint $t) {
            $t->integer('user_id')->nullable();
            $t->string('occupation')->nullable();
            $t->string('person_in_house')->nullable();
            $t->string('last_education')->nullable();
            $t->string('receipt_number')->nullable();
            $t->string('outlet_province')->nullable();
            $t->string('outlet_city')->nullable();
            $t->string('outlet_address')->nullable();
            $t->string('brand')->nullable();
            $t->string('quantity')->nullable();
            $t->string('total_price_quantity')->nullable();
            $t->string('grand_total_price')->nullable();
            $t->string('purchase_date')->nullable();
            $t->string('sent_time')->nullable();
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
