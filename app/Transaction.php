<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
	protected $table = 'transactions';
	protected $fillable = [
		'transaction_code',
		'member_code',
		'transaction_type'
	];
	public function transactionDetail()
	{
		return $this->hasMany(TransactionDetail::class, 'transaction_id', 'id');
	}
}
