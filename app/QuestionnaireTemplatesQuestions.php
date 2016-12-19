<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionnaireTemplatesQuestions extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'questionnaire_templates_questions';
}
