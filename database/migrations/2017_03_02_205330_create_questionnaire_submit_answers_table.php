<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionnaireSubmitAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_submit_answers', function (Blueprint $t) {
            $t->increments('id');
            $t->unsignedInteger('question_id');
            $t->string('answer');
            $t->timestamps();
            $t->softDeletes();

            $t->foreign('question_id')->references('id')->on('questionnaire_submit_questions')->onDelete('NO ACTION');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questionnaire_submit_answers');
    }
}
