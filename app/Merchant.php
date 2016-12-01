<?php

namespace App;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    use SoftDeletes;
    protected $table = 'merchants';
    protected $fillable = [
        'merchant_code',
        'company_name',
        'company_logo',
        'address',
        'company_email'
    ];

    public function users()
    {
        return $this->hasMany(MerchantUser::class, 'merchant_id', 'id');
    }

    public function promotions()
    {
        return $this->hasMany(Promotion::class, 'merchant_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($offer) {
            $offer->users()->delete();
        });
    }
}
