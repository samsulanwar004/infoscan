<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class LuckyDrawTransformer extends TransformerAbstract
{
	public function transform($lucky)
    {
        return [
            'id'   => $lucky->id,
            'code'   => $lucky->luckydraw_code,
            'title'   => $lucky->title,
            'description'  => $lucky->description,
            'point' => $lucky->point,
            'image' => $lucky->image,
        ];
    }
}