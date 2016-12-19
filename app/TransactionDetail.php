<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    protected $table = 'transaction_detail';
    protected $fillable = [
    	'transaction_id',
    	'member_code_from',
    	'member_code_to',
    	'amount',
    	'detail_type'
    ];

    public function transaction()
    {
    	return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
