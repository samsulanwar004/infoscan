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
        'description',
        'start_at',
        'end_at',
        'created_by',
        'total_point',
        'status'
    ];
    protected $dates = ['deleted_at'];

    public function questions()
    {
        return $this->belongsToMany(QuestionnaireQuestion::class, 'questionnaire_templates_questions', 'template_id',
            'question_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
