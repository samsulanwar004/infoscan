<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderLoyalty extends Mailable
{
    use Queueable, SerializesModels;

    protected $member;
    protected $order;

    public $subject = '[GOJAGO] Order Loyalty';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($member, $order)
    {
        $this->member = $member;
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $from = config('mail.from.address');
        $senderName = config('mail.from.name');
        return $this->from($from, $senderName)
            ->view('emails.email_order_loyalty')
            ->with([
                'member' => $this->member,
                'order' => $this->order,
            ]);
    }
}
