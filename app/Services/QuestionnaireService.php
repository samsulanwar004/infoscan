<?php

namespace App\Services;

use App\QuestionnaireTemplate;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QuestionnaireService
{
    protected $date;

    protected $s3Url;

    public function __construct()
    {
        $this->date = Carbon::now('Asia/Jakarta');
        $this->s3Url = env('S3_URL', '');
    }

    public function getAllQuestionnaires()
    {
        return QuestionnaireTemplate::all();
    }

    public function getActiveQuestionnaires()
    {
        $questionnaires = DB::table('questionnaire_templates')
            ->where('status', '=', 'publish')
            ->where('start_at', '<=', $this->date)
            ->where('end_at', '>=', $this->date)
            ->get();

        return $questionnaires;
    }

    public function getQuestionnaireDetails($id)
    {
        $questionnaireDetail = DB::table('questionnaire_templates as qt')
            ->join('questionnaire_templates_questions as qtq', 'qt.id', '=', 'qtq.template_id')
            ->join('questionnaire_questions as qq', 'qtq.question_id', '=', 'qq.id')
            ->leftJoin('questionnaire_answers as qa', 'qq.id', '=', 'qa.question_id')
            ->where('qt.id', '=', $id)
            ->select('qt.id as template_id', 'qt.description as description', 'qt.total_point as total_point',
                'qq.description as question', 'qq.id as question_id', 'qq.type as type', 'qa.id as answer_id',
                'qa.description as answer')
            ->get();

        return $questionnaireDetail;
    }
}