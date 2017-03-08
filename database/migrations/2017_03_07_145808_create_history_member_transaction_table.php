<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHistoryMemberTransactionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_action_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('member_id');
            $table->string('group', 15)->index(); // snap, redeem, authentication, etc.

            // will be a json string. should be casted to array in model.
            /*
             * format json
             * ex:
             *     {
             *         "type": "tags", // this key will determine the type of content. Can be a snap mode (tags, input, no mode, etc), authentication type (login, logout), etc.
             *         "title": "",
             *         "description": ""
             *     }
             *
             */
            $table->text('content');
            $table->timestamps();

            $table->foreign('member_id', 'FK_member_id_in_history_member_transactions')
              ->references('id')->on('members')
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
        Schema::dropIfExists('member_action_logs');
    }
}
