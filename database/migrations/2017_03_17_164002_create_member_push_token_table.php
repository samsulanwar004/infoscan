<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemberPushTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('member_push_tokens', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('member_id');
            $t->string('token');
            $t->timestamps();
            $t->softDeletes();

            $t->foreign('member_id', 'FK_member_id_on_tokens')
                ->references('id')
                ->on('members')
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
        //
    }
}
