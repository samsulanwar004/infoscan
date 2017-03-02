<?php

namespace App\Http\Controllers\Api;

use App\Services\QuestionnaireService;
use App\Transformers\QuestionnaireTransformer;
use Exception;

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
}
