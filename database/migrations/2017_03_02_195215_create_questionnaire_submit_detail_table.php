<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionnaireSubmitDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_submit_questions', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('submit_id');
            $t->string('question');
            $t->string('type');
            $t->timestamps();
            $t->softDeletes();

            $t->foreign('submit_id')->references('id')->on('questionnaire_submits')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questionnaire_submit_questions');
    }
}
