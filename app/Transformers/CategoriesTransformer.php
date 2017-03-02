<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class CategoriesTransformer extends TransformerAbstract
{
	public function transform($categories)
    {
        return [
            'id'   => $categories->id,
            'name'   => $categories->name,         
            'icon' => $categories->icon,
            'background' => $categories->background,
            'slug' => $categories->slug
        ];
    }
}