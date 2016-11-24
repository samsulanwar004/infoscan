<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSnapsTable extends Migration
{
    /**
     * Run the migrations.
     * @table snaps
     *
     * @return void
     */
    public function up()
    {
        Schema::create('snaps', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('request_code', 10);
            $table->integer('member_id')->unsigned();
            $table->string('snap_type', 15);
            $table->string('mode_type', 10);
            $table->string('status', 10)->default('new');
            $table->string('approved_by', 100)->nullable()->index();
            $table->string('check_by', 100)->nullable()->index();
            $table->timestamps();

            $table->foreign('member_id', 'FK_member_id_in_snaps')
                  ->references('id')->on('members')
                  ->onDelete('no action')
                  ->onUpdate('no action');
        });
    }

    /**
    * Reverse the migrations.
    *
    * @return void
    */
    public function down()
    {
        Schema::dropIfExists('snaps');
    }
}
