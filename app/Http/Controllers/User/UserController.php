<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CompanyDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;
use App\Models\UserPoint;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Carbon\Carbon;
use App\Models\DeliverySubscription;
use App\Models\DeliverySubscriptionPayment;
use App\Models\Coupon;
use App\Models\Credential;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;

class UserController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'required|string|max:20',
            'dob' => 'nullable|date|before:today',
            'postcode' => 'nullable|string',
            'address_1' => 'nullable|string',
            'street' => 'nullable|string',
            'city' => 'nullable|string',
            'address_2' => 'nullable|string',
        ]);

        auth()->user()->update($validated);

        return response()->json(['message' => 'Profile updated successfully']);
    }

    public function password()
    {
        return view('user.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully!'
        ]);
    }

    public function orders()
    {
        return view('user.orders');
    }

    public function orderDetails(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load(['items.options', 'items.product']);

        return view('user.order-details', compact('order'));
    }

    public function coupons()
    {
        $user = auth()->user();
        
        // Regular coupons - show to everyone
        $regularCoupons = Coupon::where('is_birthday_voucher', false)
            ->where('is_active', true)
            ->get();
        
        // Birthday vouchers - only if user has them assigned this year
        $birthdayVouchers = $user ? $user->coupons()
            ->where('is_birthday_voucher', true)
            ->where('is_active', true)
            ->wherePivot('sent_year', now()->year)
            ->get()
            : collect();
        
        return view('user.coupons', [
            'regularCoupons' => $regularCoupons,
            'birthdayVouchers' => $birthdayVouchers
        ]);
    }

    public function giftCards()
    {
        $giftCards = auth()->user()->purchasedGiftCards()
            ->orderBy('balance', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        $logo = CompanyDetails::select('company_logo')->first()->company_logo;

        return view('user.gift-cards', compact('giftCards', 'logo'));
    }

    public function points()
    {
        return view('user.points');
    }

    public function social()
    {
        return view('user.social');
    }

    public function socialShare(Request $request)
    {
        $user = auth()->user();
        $platform = $request->platform;

        if ($platform === 'facebook') {
            if (!$user->canShareToday()) {
                return response()->json(['success' => false, 'message' => 'Daily limit reached for Facebook']);
            }
        } elseif ($platform === 'tiktok') {
            if (!$user->canShareTikTokToday()) {
                return response()->json(['success' => false, 'message' => 'Daily limit reached for TikTok']);
            }
        } elseif ($platform === 'instagram') {
            if (!$user->canShareInstagramToday()) {
                return response()->json(['success' => false, 'message' => 'Daily limit reached for Instagram']);
            }
        }

        UserPoint::create([
            'user_id' => $user->id,
            'source' => 'social_share',
            'source_action' => $platform,
            'point' => 10,
            'description' => 'Shared on ' . ucfirst($platform)
        ]);

        return response()->json(['success' => true, 'message' => 'You earned 10 points!']);
    }

    public function applyReferral(Request $request)
    {
        $request->validate([
            'referral_code' => 'required|string|max:20',
        ]);

        $user = auth()->user();

        if ($user->referred_by) {
            return response()->json([
                'success' => false,
                'message' => 'You have already used a referral code.'
            ]);
        }

        $referrer = User::where('referral_code', $request->referral_code)->first();

        if (!$referrer || $referrer->id == $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid referral code.'
            ]);
        }

        UserPoint::create([
            'user_id' => $user->id,
            'referrer_id' => $referrer->id,
            'source' => 'referral',
            'point' => 50,
            'description' => 'Used referral code ' . $referrer->referral_code,
        ]);

        UserPoint::create([
            'user_id' => $referrer->id,
            'referrer_id' => $user->id,
            'source' => 'referral',
            'point' => 50,
            'description' => 'Referral bonus for ' . $user->name,
        ]);

        $user->referred_by = $referrer->id;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Referral applied! You and ' . $referrer->name . ' earned 50 points each.'
        ]);
    }

    public function subscription()
    {
        $subscription = auth()->user()->deliverySubscription()->first();

        return view('user.subscription', compact('subscription'));
    }

    public function subscriptionCheckout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:stripe,paypal'
        ]);

        $user = auth()->user();
        $amount = 5.00;

        return DB::transaction(function () use ($request, $user, $amount) {
            $payment = Payment::create([
                'user_id'        => $user->id,
                'payment_type'   => 'subscription',
                'reference_id'   => 0,
                'amount'         => $amount,
                'currency'       => 'GBP',
                'payment_method' => $request->payment_method,
                'status'         => 'pending',
                'metadata'       => [
                    'subscription_type' => 'Free Delivery',
                    'duration'          => '1 month',
                    'ip_address'        => $request->ip()
                ],
            ]);

            session([
                'active_payment_id'     => $payment->id,
                'subscription_checkout' => [
                    'amount'         => $amount,
                    'user_id'        => $user->id,
                    'payment_method' => $request->payment_method
                ]
            ]);

            if ($request->payment_method === 'stripe') {
                return $this->initiateStripeSubscription($user, $amount);
            }
            
            return $this->initiatePayPalSubscription($user, $amount);
        });
    }

    private function initiateStripeSubscription($user, $amount)
    {
        $stripeCredential = Credential::where('gateway', 'Stripe')->first();
        $paymentId = session('active_payment_id');

        Stripe::setApiKey($stripeCredential->client_secret);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'GBP',
                    'product_data' => [
                        'name' => 'Free Delivery Subscription',
                        'description' => '1 month unlimited free delivery'
                    ],
                    'unit_amount' => intval($amount * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('user.subscription.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('user.subscription.cancel'),
            'customer_email' => $user->email,
            'metadata' => ['payment_id' => $paymentId]
        ]);

        return response()->json(['success' => true, 'redirectUrl' => $session->url]);
    }

    private function initiatePayPalSubscription($user, $amount)
    {
        session([
            'subscription_checkout' => [
                'amount' => $amount,
                'user_id' => $user->id,
                'payment_method' => 'paypal'
            ]
        ]);

        try {
            $this->setPayPalConfig();

            $provider = new \Srmklive\PayPal\Services\PayPal;
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('user.subscription.success'),
                    "cancel_url" => route('user.subscription.cancel'),
                ],
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "GBP",
                            "value" => number_format((float)$amount, 2, '.', '')
                        ],
                        "description" => 'Free Delivery Subscription - 1 month'
                    ]
                ]
            ]);

            \Log::info('PayPal Subscription Response:', $response);

            if (isset($response['id']) && $response['id'] != null) {
                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {
                        return response()->json([
                            'success' => true,
                            'redirectUrl' => $links['href']
                        ]);
                    }
                }
            }

            \Log::error('PayPal Subscription Order Creation Failed:', ['response' => $response]);
            
            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'Failed to create PayPal order'
            ], 500);

        } catch (\Exception $e) {
            \Log::error('PayPal Subscription Exception:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'PayPal error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function subscriptionSuccess(Request $request)
    {
        $paymentId = session('active_payment_id');
        $checkoutData = session('subscription_checkout');

        if (!$paymentId || !$checkoutData) {
            return redirect()->route('user.subscription')->with('error', 'Invalid payment session');
        }

        return DB::transaction(function () use ($request, $paymentId, $checkoutData) {
            $payment = Payment::findOrFail($paymentId);
            
            if ($payment->payment_method === 'paypal') {
                $this->handlePayPalSubscription($request, $checkoutData);
                $payment->transaction_id = $request->token;
            } else {
                $payment->transaction_id = $request->session_id;
            }

            $payment->status = 'completed';
            $payment->save();

            $user = auth()->user();
            $existing = $user->deliverySubscription()->first();

            if ($existing && $existing->isActive()) {
                $newEndDate = $existing->ends_at->addMonthNoOverflow();
                $existing->update(['ends_at' => $newEndDate, 'last_billed_at' => now()]);
                $subscription = $existing;
            } else {
                $subscription = DeliverySubscription::create([
                    'user_id' => $user->id,
                    'amount' => $payment->amount,
                    'status' => 'active',
                    'started_at' => now(),
                    'ends_at' => now()->addMonthNoOverflow(),
                    'last_billed_at' => now()
                ]);
            }

            DeliverySubscriptionPayment::create([
                'delivery_subscription_id' => $subscription->id,
                'amount' => $payment->amount,
                'status' => 'paid',
                'billing_month' => now()->startOfMonth(),
                'paid_at' => now(),
                'payment_ref' => $payment->transaction_id
            ]);

            $payment->update(['reference_id' => $subscription->id]);

            session()->forget(['subscription_checkout', 'active_payment_id']);

            return redirect()->route('user.subscription')->with('success', 'Subscription activated!');
        });
    }

    private function handlePayPalSubscription(Request $request, $checkoutData)
    {
        $token = $request->input('token');

        if (!$token) {
            throw new \Exception('PayPal token missing');
        }

        $this->setPayPalConfig();

        $provider = new \Srmklive\PayPal\Services\PayPal;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($token);

        \Log::info('PayPal Subscription Capture Response:', $response);

        if (!isset($response['status']) || $response['status'] !== 'COMPLETED') {
            throw new \Exception($response['message'] ?? 'PayPal payment capture failed');
        }
    }

    private function setPayPalConfig()
    {
        $credential = Credential::where('gateway', 'Paypal')->first();

        if (!$credential || !$credential->client_id || !$credential->client_secret) {
            throw new \Exception('PayPal credentials not configured');
        }

        config([
            'paypal.mode' => $credential->mode,
            'paypal.sandbox.client_id' => $credential->client_id,
            'paypal.sandbox.client_secret' => $credential->client_secret,
            'paypal.live.client_id' => $credential->client_id,
            'paypal.live.client_secret' => $credential->client_secret,
        ]);
    }

    public function subscriptionCancel()
    {
        $paymentId = session('active_payment_id');
        if ($paymentId) {
            Payment::where('id', $paymentId)->update(['status' => 'failed']);
        }
        session()->forget(['subscription_checkout', 'active_payment_id']);
        return redirect()->route('user.subscription')->with('error', 'Payment cancelled');
    }
}