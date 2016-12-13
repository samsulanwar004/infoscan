<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionnaireQuestion extends Model
{
    use SoftDeletes;

    protected $table = 'questionnaire_questions';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'description',
        'type',
        'questionnaire_code'
    ];

    public function answer()
    {
        return $this->hasMany(QuestionnaireAnswer::class, 'questionnaire_template_questions', 'template_id');
    }

    public function template()
    {
        return $this->belongsToMany(QuestionnaireTemplateQuestions::class, 'questionnaire_template_questions', 'question_id', 'template_id');
    }
}
