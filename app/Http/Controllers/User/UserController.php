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
        return view('user.coupons');
    }

    public function giftCards()
    {
        $giftCards = auth()->user()->purchasedGiftCards()
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
            'amount' => 'required|numeric|min:5'
        ]);

        $user = auth()->user();
        $amount = 5.00;

        try {
            session([
                'subscription_checkout' => [
                    'amount' => $amount,
                    'user_id' => $user->id
                ]
            ]);

            Stripe::setApiKey(config('services.stripe.secret'));

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
                'success_url' => route('user.subscription.success'),
                'cancel_url' => route('user.subscription.cancel'),
                'customer_email' => $user->email,
            ]);

            return response()->json([
                'success' => true,
                'redirectUrl' => $session->url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment session'
            ], 500);
        }
    }

    public function subscriptionSuccess()
    {
        $checkoutData = session('subscription_checkout');

        if (!$checkoutData) {
            return redirect()->route('user.subscription')->with('error', 'Invalid payment session');
        }

        try {
            $user = auth()->user();
            $amount = $checkoutData['amount'];
            $existingSubscription = $user->deliverySubscription()->first();

            if ($existingSubscription && $existingSubscription->isActive()) {
                $currentEndDate = $existingSubscription->ends_at;
                $newEndDate = $currentEndDate->copy()->addMonthNoOverflow();

                $existingSubscription->update([
                    'ends_at' => $newEndDate,
                    'last_billed_at' => now()
                ]);

                $subscription = $existingSubscription;
                $renewalStartMonth = $currentEndDate;
            } else {
                $newEndDate = now()->copy()->addMonthNoOverflow();

                $subscription = DeliverySubscription::create([
                    'user_id' => $user->id,
                    'amount' => $amount,
                    'status' => 'active',
                    'started_at' => now(),
                    'ends_at' => $newEndDate,
                    'last_billed_at' => now()
                ]);

                $renewalStartMonth = now();
            }

            DeliverySubscriptionPayment::create([
                'delivery_subscription_id' => $subscription->id,
                'amount' => $amount,
                'status' => 'paid',
                'billing_month' => $renewalStartMonth->copy()->startOfMonth()->addMonthNoOverflow(),
                'paid_at' => now(),
                'payment_ref' => 'STRIPE_' . uniqid()
            ]);

            session()->forget('subscription_checkout');

            return redirect()->route('user.subscription')
                ->with('success', 'Subscription extended! Valid until ' . $subscription->ends_at->format('M d, Y'));
        } catch (\Exception $e) {
            return redirect()->route('user.subscription')
                ->with('error', 'Error processing subscription');
        }
    }

    public function subscriptionCancel()
    {
        session()->forget('subscription_checkout');
        return redirect()->route('user.subscription')
            ->with('error', 'Payment cancelled');
    }
}