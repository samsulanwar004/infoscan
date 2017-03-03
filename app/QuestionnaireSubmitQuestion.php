<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionnaireSubmitQuestion extends Model
{
    use SoftDeletes;

    protected $table = 'questionnaire_submit_questions';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'submit_id',
        'question',
        'type'
    ];

    public function submit()
    {
        return $this->belongsTo(QuestionnaireSubmit::class, 'submit_id');
    }

    public function answer()
    {
        return $this->hasMany(QuestionnaireSubmitAnswer::class, 'question_id');
    }

}
