<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MerchantUser extends Model
{
    use SoftDeletes;

    protected $table = 'merchant_users';

    protected $fillable = ['merchant_id', 'user_id'];

    public $timestamps = false;

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($offer) {
            $offer->user()->delete();
        });
    }
}
