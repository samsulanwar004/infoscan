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
    protected $password;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $password_str)
    {
        $this->user = $user;
        $this->password = $password_str;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('admin@rebelworks.co')
            ->view('emails.merchant_user', ['user' => $this->user, 'password' => $this->password]);
    }
}
