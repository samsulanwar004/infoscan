<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class QuestionnaireTransformer extends TransformerAbstract
{
    public function transform($questionnaire)
    {
        return [
            'id' => $questionnaire->id,
            'description' => $questionnaire->description,
            'total_point' => $questionnaire->total_point
        ];
    }
}