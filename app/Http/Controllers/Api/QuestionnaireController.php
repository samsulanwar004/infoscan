<?php

namespace App\Http\Controllers\Api;

use App\QuestionnaireSubmit;
use App\QuestionnaireSubmitAnswer;
use App\QuestionnaireSubmitQuestion;
use App\Services\QuestionnaireService;
use App\Transformers\QuestionnaireTransformer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Jobs\MemberActionJob;

class QuestionnaireController extends BaseApiController
{
    public function index()
    {
        try {
            $questionnaires = (new QuestionnaireService)->getActiveQuestionnaires();

            $transform = fractal()
                ->collection($questionnaires)
                ->transformWith(new QuestionnaireTransformer())
                ->toArray();

            return $this->success($transform, 200);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $questionnaireDetail = (new QuestionnaireService)->getQuestionnaireDetails($id);

            $firstTemplate = $questionnaireDetail->first();

            if (!$firstTemplate) {
                return $this->error('Questionnaire is empty!', 400);
            }

            $questionnaireResult = [
                'template_id' => $firstTemplate->template_id,
                'template_description' => $firstTemplate->description,
                'total_point' => $firstTemplate->total_point
            ];

            $detail = [];
            foreach ($questionnaireDetail->groupBy('question_id') as $question) {
                $q = [
                    'question_id' => $question->first()->question_id,
                    'question' => $question->first()->question,
                    'question_type' => $question->first()->type
                ];

                $answers = [];
                foreach ($question as $answer) {
                    $a = [
                        'answer_id' => $answer->answer_id,
                        'answer' => $answer->answer,
                    ];

                    $answers[] = $a;
                }

                $q['answers'] = $answers;
                $detail[] = $q;
            }

            $questionnaireResult['questions'] = $detail;

            return $this->success($questionnaireResult);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $validation = \Validator::make($request->all(), [
            'template' => 'required',
            'total_point' => 'required',
            'questions.*.type' => 'required',
            'questions.*.description' => 'required',
            'questions.*.answers.*' => 'required'
        ]);

        if ($validation->fails()) {
            return $this->error('Bad Request', 400);
        }

        $member = $this->getActiveMember();

        if (!$member) {
            return $this->error('Unauthenticated', 401);
        }

        DB::beginTransaction();
        try {

            $submit = new QuestionnaireSubmit;
            $submit->member_id = $member->id;
            $submit->template_id = $request->input('template');
            $submit->total_point = $request->input('total_point');
            $submit->save();

            $questions = $request->input('questions');

            foreach ($questions as $question) {
                $submitQuestion = new QuestionnaireSubmitQuestion;
                $submitQuestion->submit_id = $submit->id;
                $submitQuestion->type = $question['type'];
                $submitQuestion->question = $question['description'];
                $submitQuestion->save();

                foreach ($question['answers'] as $answer) {
                    $submitAnswer = new QuestionnaireSubmitAnswer;
                    $submitAnswer->question_id = $submitQuestion->id;
                    $submitAnswer->answer = $answer;
                    $submitAnswer->save();
                }
            }

            DB::commit();

            //build data for member history
            $content = [
                'type' => 'survey',
                'title' => 'Survey',
                'description' => 'Kamu telah mendapatkan poin dari mengisi survei. Petok!',
                'flag' => 1,
            ];

            $config = config('common.queue_list.member_action_log');
            $job = (new MemberActionJob($member->id, 'portalpoint', $content))->onQueue($config)->onConnection(env('INFOSCAN_QUEUE'));
            dispatch($job); 

            return $this->success();

        } catch (Exception $e) {

            DB::rollback();

            return $this->error($e->getMessage() . $e->getFile() . $e->getLine());
        }
    }
}
