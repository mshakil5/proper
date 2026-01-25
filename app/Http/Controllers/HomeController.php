<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Contact;
use App\Models\User;
use Carbon\Carbon;
use App\Models\Coupon;
use Illuminate\Support\Facades\Mail;
use App\Mail\BirthdayVoucherMail;
use App\Models\DeliverySubscription;
use App\Mail\SubscriptionReminderMail;

class HomeController extends Controller
{
    public function dashboard()
    { 
        if (Auth::check()) {
            $user = auth()->user();

            if ($user->user_type == '1') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        } else {
            return redirect()->route('login');
        }
    }
    
    public function adminHome()
    {
        $upcomingBirthdays = User::where('user_type', 2)
            ->whereNotNull('dob')
            ->get()
            ->filter(function($user) {
                $daysUntil = $user->days_until_birthday;
                return $daysUntil !== null && $daysUntil >= 0 && $daysUntil <= 7;
            })
            ->sortBy('days_until_birthday')
            ->values();
        $this->sendBirthdayVouchers();
        $this->sendSubscriptionReminderEmails();
        return view('admin.pages.dashboard', compact('upcomingBirthdays'));
    }

    private function sendBirthdayVouchers()
    {
        $tomorrow = \Carbon\Carbon::tomorrow();
        
        $birthdayUsers = User::where('user_type', 2)
            ->whereNotNull('dob')
            ->get()
            ->filter(function($user) use ($tomorrow) {
                $dob = \Carbon\Carbon::parse($user->dob);
                return $dob->month === $tomorrow->month && $dob->day === $tomorrow->day;
            });

        $birthdayVoucher = Coupon::where('is_birthday_voucher', true)
            ->where('is_active', true)
            ->first();

        if (!$birthdayVoucher || $birthdayUsers->isEmpty()) {
            return;
        }

        foreach ($birthdayUsers as $user) {
            $alreadySent = $user->coupons()
                ->where('coupon_id', $birthdayVoucher->id)
                ->wherePivot('sent_year', now()->year)
                ->first();

            if (!$alreadySent) {
                $user->coupons()->attach($birthdayVoucher->id, [
                    'sent_at' => now(),
                    'sent_year' => now()->year,
                    'used_count' => 0
                ]);

                Mail::to($user->email)->send(new BirthdayVoucherMail($user, $birthdayVoucher));
            }
        }
    }

    private function sendSubscriptionReminderEmails()
    {
        $sevenDaysFromNow = \Carbon\Carbon::now()->addDays(7)->startOfDay();
        
        $subscriptions = DeliverySubscription::where('status', 'active')
            ->whereDate('ends_at', $sevenDaysFromNow)
            ->where('sent_7_day_reminder', false)
            ->get();

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            
            if ($user && $user->email) {
                Mail::to($user->email)->send(new SubscriptionReminderMail($user, $subscription, 7));
                
                $subscription->update(['sent_7_day_reminder' => true]);
            }
        }

        $tomorrowStart = \Carbon\Carbon::tomorrow()->startOfDay();
        
        $subscriptions = DeliverySubscription::where('status', 'active')
            ->whereDate('ends_at', $tomorrowStart)
            ->where('sent_1_day_reminder', false)
            ->get();

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;
            
            if ($user && $user->email) {
                Mail::to($user->email)->send(new SubscriptionReminderMail($user, $subscription, 1));
                
                $subscription->update(['sent_1_day_reminder' => true]);
            }
        }
    }

    public function userHome()
    {
        return view('user.dashboard');
    }
}
