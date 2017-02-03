<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merchant_id')->nullable()->unsigned();
            $table->string('title')->index();
            $table->text('description')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->string('url')->index()->nullable();
            $table->string('created_by', 150);
            $table->string('updated_by', 150)->nullable();
            $table->boolean('is_active')->default(1);
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('merchant_id', 'FK_merchant_id_in_promotions')
              ->references('id')
              ->on('merchants')
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
        Schema::dropIfExists('promotions');
    }
}
