<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionnaireAnswer extends Model
{
    protected $table = 'questionnaire_answers';
    public $timestamps = false;
    protected $fillable = [
        'description',
        'question_id'
    ];

    public function question()
    {
        return $this->belongsTo(QuestionnaireQuestion::class, 'question_id');
    }
}
