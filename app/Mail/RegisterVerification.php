<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegisterVerification extends Mailable
{
    use Queueable, SerializesModels;

    protected $member;

    public $subject = '[GOJAGO] Verifikasi Akun Gojago';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($member)
    {
        $this->member = $member;
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
                    ->view('emails.email_verification')
                    ->with([
                        'member' => $this->member,
                    ]);
    }
}
