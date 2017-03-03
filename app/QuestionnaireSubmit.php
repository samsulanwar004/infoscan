<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuestionnaireSubmit extends Model
{
    use SoftDeletes;

    protected $table = 'questionnaire_submits';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'member_id',
        'template_id',
        'total_point'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }

    public function template()
    {
        return $this->belongsTo(QuestionnaireTemplate::class, 'template_id');
    }

    public function detail()
    {
        return $this->hasMany(QuestionnaireSubmitDetail::class, 'submit_id');
    }
}
