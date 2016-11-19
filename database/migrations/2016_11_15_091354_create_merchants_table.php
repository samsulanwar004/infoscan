<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('merchant_code', 10)->unique();
            $table->string('company_name', 200);
            $table->string('company_logo')->nullable();
            $table->text('address')->nullable();
            $table->string('company_email', 100)->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('merchant_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merchant_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->foreign('merchant_id')->references('id')->on('merchants');
            $table->foreign('user_id')->references('id')->on('users');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_users');
        Schema::dropIfExists('merchants');
    }
}
