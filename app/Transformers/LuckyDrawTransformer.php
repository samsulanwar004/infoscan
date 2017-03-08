<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Services\LuckyDrawService;

class LuckyDrawTransformer extends TransformerAbstract
{
	public function transform($lucky)
    {
        $memberId = auth('api')->user()->id;
        $check = (new LuckyDrawService)->isMultipleLuckyDraw($memberId, $lucky->id);
        return [
            'id'   => $lucky->id,
            'code'   => $lucky->luckydraw_code,
            'title'   => $lucky->title,
            'description'  => $lucky->description,
            'point' => $lucky->point,
            'image' => $lucky->image,
            'start_at' => $lucky->start_at,
            'end_at' => $lucky->end_at,
            'multiple' => $lucky->is_multiple,
            'flag' => ($check == true) ? 1 :0,
        ];
    }
}