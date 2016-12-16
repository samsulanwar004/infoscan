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
        'questionnaire_question_code'
    ];

    public function answer()
    {
        return $this->hasMany(QuestionnaireAnswer::class, 'question_id');
    }

    public function template()
    {
        return $this->belongsToMany(QuestionnaireTemplate::class, 'questionnaire_templates_questions', 'question_id', 'template_id');
    }
}
