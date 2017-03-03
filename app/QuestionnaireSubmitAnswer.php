<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionnaireSubmitAnswer extends Model
{
    use SoftDeletes;

    protected $table = 'questionnaire_submit_answers';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'question_id',
        'answer'
    ];

    public function question()
    {
        return $this->belongsTo(QuestionnaireSubmitQuestion::class, 'question_id');
    }
}
