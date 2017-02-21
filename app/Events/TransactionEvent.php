<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;

class TransactionEvent
{
    use SerializesModels;

    public $memberCode;

    public $transactionType;

    public $snapId;

    public $transactionCode;

    /**
     * Create a new event instance.
     *
     * @param  integer $memberCode
     * @param  integer $transactionType
     * @param  integer $snapId
     * @internal param string $transactionCode
     */
    public function __construct($memberCode, $transactionType, $snapId)
    {
        $this->transactionCode = strtolower(str_random(10));
        $this->memberCode = $memberCode;
        $this->transactionType = $transactionType;
        $this->snapId = $snapId;
    }
}