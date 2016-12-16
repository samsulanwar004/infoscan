<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionnaireAnswer extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'questionnaire_answers';
    public $timestamps = false;
    protected $fillable = [
        'description',
        'question_id'
    ];

    public function question()
    {
        return $this->belongsTo(QuestionnaireQuestion::class, 'id');
    }
}
