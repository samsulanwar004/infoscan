<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    protected $table = 'merchants';
    protected $fillable = [
        'merchant_code', 'company_name', 'company_logo', 'address', 'company_email'
    ];
}
