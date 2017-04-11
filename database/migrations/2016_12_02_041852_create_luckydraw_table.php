<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLuckydrawTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lucky_draws', function (Blueprint $table) {
            $table->increments('id');
            $table->string('luckydraw_code', 10)->unique();
            $table->string('title', 150);
            $table->string('description')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->decimal('point', 15, 2)->default(0);
            $table->string('image')->nullable();
            $table->string('created_by', 150)->index();
            $table->boolean('is_multiple')->default(0);
            $table->boolean('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('members_lucky_draws', function(Blueprint $t) {
            $t->increments('id');
            $t->integer('member_id')->unsigned();
            $t->integer('luckydraw_id')->unsigned();
            $t->string('random_number', 8);
            $t->timestamps();

            $t->foreign('member_id', 'FK_member_id_in_member_lucky_draws')
              ->references('id')
              ->on('members')
              ->onDelete('NO ACTION');

            $t->foreign('luckydraw_id', 'FK_luckydraw_id_in_member_lucky_draws')
              ->references('id')
              ->on('lucky_draws')
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
        Schema::dropIfExists('members_lucky_draws');
        Schema::dropIfExists('lucky_draws');
    }
}
