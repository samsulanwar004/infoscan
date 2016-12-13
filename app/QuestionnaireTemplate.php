<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionnaireTemplate extends Model
{
    use SoftDeletes;

    protected $table = 'questionnaire_templates';
    protected $fillable = [
        'questionnaire_template_code',
        'start_at',
        'end_at',
        'created_by',
        'total_point'
    ];
    protected $dates = ['deleted_at'];

    public function questions()
    {
        return $this->belongsToMany(QuestionnaireTemplateQuestions::class, 'questionnaire_template_questions', 'template_id', 'question_id');
    }
}
