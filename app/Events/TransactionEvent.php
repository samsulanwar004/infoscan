<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class TransactionEvent
{
    use SerializesModels;

    public $memberCode;

    public $transactionType;

    public $snapId;

    /**
     * Create a new event instance.
     *
     * @param  integer  $memberCode
     * @param  integer  $transactionType
     * @param  integer $snap_id
     * @param  array  $detail
     * @return void
     */
    public function __construct($memberCode, $transactionType, $snapId)
    {
        $this->transaction_code = strtolower(str_random(10));
        $this->member_code = $memberCode;
        $this->transaction_type = $transactionType;
        $this->snap_id = $snapId;
    }
}