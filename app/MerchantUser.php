<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MerchantUser extends Model
{
    protected $table = 'merchant_users';

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}