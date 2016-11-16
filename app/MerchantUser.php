<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MerchantUser extends Model
{
    protected $table = 'merchant_users';

    public function merchant()
    {
        return $this->hasOne(Merchant::class, 'merchant_id');
    }
}