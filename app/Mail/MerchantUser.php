<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MerchantUser extends Mailable
{
    use Queueable, SerializesModels;
    protected $user;
    protected $passwordStr;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $passwordStr)
    {
        $this->user = array(
            'user' => $user,
            'password' => $passwordStr
        );
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('admin@rebelworks.co')
            ->subject('Your Merchant User Account')
            ->view('emails.merchant_user', ['user' => $this->user]);
    }
}
