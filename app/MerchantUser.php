<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MerchantUser extends Model
{
    protected $table = 'merchant_users';

    public $timestamps = false;
    
    public function merchant()
    {
      return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function user()
    {
      return $this->belongsTo(User::class, 'user_id');
    }
}