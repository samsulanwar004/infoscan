<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class PromotionTransformer extends TransformerAbstract
{
	public function transform($promotion)
    {
        return [
            'id'   => $promotion->id,
            'title'   => $promotion->title,
            'description'  => $promotion->description,
            'image'  => $promotion->image,
            'url' => $promotion->url,            
            'point' => $promotion->point,            
            'start_at' => $promotion->start_at,
            'end_at' => $promotion->end_at,
            'category' => $promotion->category,
        ];
    }
}