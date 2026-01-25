<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BirthdayVoucherMail extends Mailable
{
    use SerializesModels;

    public $user;
    public $voucher;

    public function __construct($user, $voucher)
    {
        $this->user = $user;
        $this->voucher = $voucher;
    }

    public function build()
    {
        return $this->subject('Happy Birthday! 🎉 Here\'s Your Special Gift - £' . $this->voucher->discount_value)
            ->view('emails.birthday-voucher')
            ->with([
                'user' => $this->user,
                'voucher' => $this->voucher,
            ]);
    }
}