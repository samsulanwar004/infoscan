<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Services\QuestionnaireService;

class QuestionnaireTransformer extends TransformerAbstract
{
    public function transform($questionnaire)
    {
    	$memberId = auth('api')->user()->id;
        $check = (new QuestionnaireService)->isMultipleQuestionnaire($memberId, $questionnaire->id);
        return [
            'id' => $questionnaire->id,
            'description' => $questionnaire->description,
            'total_point' => $questionnaire->total_point,
            'flag' => ($check == true) ? 1 :0,
        ];
    }
}