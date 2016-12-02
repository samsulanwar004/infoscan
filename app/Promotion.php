<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $table = 'promotions';

    public function merchant()
    {
    	return belongsTo(Merchant::class, 'merchant_id');
    }
}
