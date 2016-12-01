<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuestionnaireTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questionnaire_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->text('questionnaire_template_code')->unique();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->text('created_by', 150);
            $table->integer('total_point');
            $table->timestamps();
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
        Schema::dropIfExists('questionnaire_templates');
    }
}
