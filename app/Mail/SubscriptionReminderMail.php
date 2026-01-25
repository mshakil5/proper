<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionReminderMail extends Mailable
{
    use SerializesModels;

    public $user;
    public $subscription;
    public $daysRemaining;

    public function __construct($user, $subscription, $daysRemaining)
    {
        $this->user = $user;
        $this->subscription = $subscription;
        $this->daysRemaining = $daysRemaining;
    }

    public function build()
    {
        return $this->subject('Renewal Reminder: Your Free Delivery Pass expires in ' . $this->daysRemaining . ' day' . ($this->daysRemaining > 1 ? 's' : ''))
            ->view('emails.subscription-reminder')
            ->with([
                'user' => $this->user,
                'subscription' => $this->subscription,
                'daysRemaining' => $this->daysRemaining
            ]);
    }
}