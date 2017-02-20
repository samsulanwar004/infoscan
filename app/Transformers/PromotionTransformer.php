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
            'icon' => $promotion->icon,
            'background' => $promotion->background,
        ];
    }
}