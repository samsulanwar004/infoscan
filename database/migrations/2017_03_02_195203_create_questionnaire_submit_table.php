<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionnaireSubmitTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_submits', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('member_id');
            $t->unsignedInteger('template_id');
            $t->integer('total_point');
            $t->timestamps();
            $t->softDeletes();

            $t->foreign('member_id')->references('id')->on('members')->onDelete('NO ACTION');
            $t->foreign('template_id')->references('id')->on('questionnaire_templates')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questionnaire_submits');
    }
}
