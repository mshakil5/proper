<?php

namespace App\Http\Controllers;

use SEOMeta;
use Twitter;
use OpenGraph;
use Stripe\Stripe;
use App\Models\Tag;
use App\Models\Plan;
use App\Models\Banner;
use App\Models\Master;
use App\Models\Slider;
use App\Models\Booking;
use App\Models\Contact;
use App\Models\Section;
use App\Models\Service;
use App\Models\FaqQuestion;
use Stripe\Checkout\Session;
use App\Models\ContentCategory;
use App\Models\CompanyDetails;
use Illuminate\Support\Facades\Cache;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\ContactEmail;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Http;

use App\Models\DeliveryZone;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use Illuminate\Support\Str;
use App\Models\UserPoint;
use App\Models\GiftcardPackage;
use App\Models\GiftCard;
use App\Mail\OrderConfirmationMail;
use App\Models\Credential;
use App\Helpers\PayPalHelper;
use App\Models\CouponUsage;
use Carbon\Carbon;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use App\Models\ProductOptionItem;

class FrontendController extends Controller
{
    public function index()
    {
      $company = CompanyDetails::first();
      $hero = Master::firstOrCreate(['name' => 'hero']);
      $findUs = Master::firstOrCreate(['name' => 'find-us']);
      $sliders = Slider::where('status', 1)->latest()->get();

      $sections = Section::where('status', 1)
          ->orderBy('sl', 'asc')
          ->get();

      $this->seo(
          $company?->meta_title ?? '',
          $company?->meta_description ?? '',
          $company?->meta_keywords ?? '',
          $company?->meta_image ? asset('uploads/company/meta/' . $company->meta_image) : null
      );

      return view('frontend.index', compact('hero', 'findUs', 'sliders','sections','company'));
    }

    public function productDetails($slug)
    {
        $product = Product::with('category', 'tag', 'options.items.product')
            ->where('slug', $slug)
            ->firstOrFail();

        $this->seo(
            $product->title . ' - ' . config('app.name'),
            $product->short_description ?? $product->long_description,
            $product->title . ', ' . $product->category->name,
            asset($product->image)
        );

        return view('frontend.product-details', compact('product'));
    }

    public function checkDelivery(Request $request)
    {
        $request->validate([
            'postcode' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $centerLatitude = 53.223912;
        $centerLongitude = -0.532985;
        $deliveryRadius = 7.5;

        $user = auth()->user();

        $lat1Rad = deg2rad($centerLatitude);
        $lon1Rad = deg2rad($centerLongitude);
        $lat2Rad = deg2rad((float) $request->latitude);
        $lon2Rad = deg2rad((float) $request->longitude);

        $dlat = $lat2Rad - $lat1Rad;
        $dlon = $lon2Rad - $lon1Rad;

        $a = sin($dlat / 2) ** 2 +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($dlon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = 3959 * $c;

        if ($distance <= $deliveryRadius) {
            if ($user && $user->hasActiveDeliverySubscription()) {
                $deliveryCharge = 0.00;
            } elseif ($distance <= 4) {
                $deliveryCharge = 2.00;
            } else {
                $deliveryCharge = 3.00;
            }

            return response()->json([
                'available' => true,
                'delivery_charge' => $deliveryCharge,
                'distance' => round($distance, 2),
                'message' => 'Delivery available'
            ]);
        }

        return response()->json([
            'available' => false,
            'message' => 'Outside delivery area'
        ], 422);
    }

    public function getAddresses(Request $request)
    {
        $request->validate([
            'postcode' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $postcode = strtoupper(trim($request->postcode));

        $centerLatitude  = 53.223912;
        $centerLongitude = -0.532985;
        $deliveryRadius  = 7.5;

        $user = auth()->user();

        $latitude  = (float) $request->latitude;
        $longitude = (float) $request->longitude;

        $lat1Rad = deg2rad($centerLatitude);
        $lon1Rad = deg2rad($centerLongitude);
        $lat2Rad = deg2rad($latitude);
        $lon2Rad = deg2rad($longitude);

        $dlat = $lat2Rad - $lat1Rad;
        $dlon = $lon2Rad - $lon1Rad;

        $a = sin($dlat / 2) * sin($dlat / 2) +
            cos($lat1Rad) * cos($lat2Rad) *
            sin($dlon / 2) * sin($dlon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = 3959 * $c;

        if ($distance > $deliveryRadius) {
            return response()->json([
                'message'  => 'Outside delivery area',
                'distance' => round($distance, 2),
            ], 422);
        }

        if ($user && $user->hasActiveDeliverySubscription()) {
            $deliveryCharge = 0.00;
        } elseif ($distance <= 4) {
            $deliveryCharge = 2.00;
        } else {
            $deliveryCharge = 3.00;
        }

        $addresses = $this->getAddressesFromNominatim($latitude, $longitude, $postcode);

        if (empty($addresses)) {
            return response()->json([
                'message' => 'No addresses found for this area',
            ], 422);
        }

        return response()->json([
            'available'       => true,
            'delivery_charge' => (float) $deliveryCharge,
            'distance'        => round($distance, 2),
            'postcode'        => $postcode,
            'addresses'       => $addresses,
            'message'         => 'Delivery available',
        ]);
    }

    private function getAddressesFromNominatim($latitude, $longitude, $postcode)
    {
        try {
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat=" . $latitude . "&lon=" . $longitude . "&zoom=18&addressdetails=1";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Restaurant App');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            curl_close($ch);

            if (!$response) {
                return [];
            }

            $data = json_decode($response, true);

            if (!$data || !isset($data['address'])) {
                return [];
            }

            $address = $data['address'];
            $city = $address['city'] ?? $address['town'] ?? $address['village'] ?? '';
            $street = $address['road'] ?? '';
            $houseNumber = $address['house_number'] ?? '1';

            if (!$street || !$city) {
                return [];
            }

            $addresses = [];
            $baseNumber = (int) $houseNumber;

            for ($i = 0; $i < 5; $i++) {
                $addresses[] = [
                    'id' => $i,
                    'street' => ($baseNumber + ($i * 2)) . ' ' . $street,
                    'city' => $city,
                    'postcode' => $postcode,
                    'display' => ($baseNumber + ($i * 2)) . ' ' . $street . ', ' . $city
                ];
            }

            return $addresses;

        } catch (\Exception $e) {
            return [];
        }
    }

    public function product(Request $request)
    {
        $product = Product::with('category', 'tag', 'options.items.product')->find($request->id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $html = view('frontend.product', compact('product'))->render();

        return response()->json(['html' => $html]);
    }

    public function menu()
    {
        $menu = Master::firstOrCreate(['name' => 'Menu']);
        $this->seo(
            $menu->meta_title,
            $menu->meta_description,
            $menu->meta_keywords,
            $menu->meta_image ? asset('uploads/meta_image/' . $menu->meta_image) : null
        );

        $products = Product::where('status', 1)
            ->orderBy('sl', 'asc')
            ->get();
       return view('frontend.menu', compact('products'));
    }

    public function ourStory()
    {
        $menu = Master::firstOrCreate(['name' => 'our-story']);
        $this->seo(
            $menu->meta_title,
            $menu->meta_description,
            $menu->meta_keywords,
            $menu->meta_image ? asset('uploads/meta_image/' . $menu->meta_image) : null
        );
        return view('frontend.our-story');
    }

    public function giftCards()
    {
        $menu = Master::firstOrCreate(['name' => 'gift-cards']);
        $this->seo(
            $menu->meta_title,
            $menu->meta_description,
            $menu->meta_keywords,
            $menu->meta_image ? asset('uploads/meta_image/' . $menu->meta_image) : null
        );
        $packages = GiftcardPackage::where('is_active', true)->orderBy('amount', 'asc')->get();
        $logo = CompanyDetails::select('company_logo')->first()->company_logo;
        return view('frontend.gift-cards', compact('packages', 'logo'));
    }

    public function giftCardCheckout(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:giftcard_packages,id',
            'payment_method' => 'required|in:stripe,paypal'
        ]);

        return DB::transaction(function () use ($request) {
            $package = GiftcardPackage::where('id', $request->package_id)
                                    ->where('is_active', true)
                                    ->firstOrFail();
            
            $user = auth()->user();

            $payment = Payment::create([
                'user_id'        => $user->id,
                'payment_type'   => 'giftcard',
                'reference_id'   => $package->id,
                'amount'         => $package->amount,
                'currency'       => 'GBP',
                'payment_method' => $request->payment_method,
                'status'         => 'pending',
                'metadata'       => [
                    'package_id'   => $package->id,
                    'package_name' => $package->name,
                    'ip_address'   => $request->ip()
                ],
            ]);

            session([
                'active_payment_id' => $payment->id,
                'giftcard_checkout' => [
                    'package_id'     => $package->id,
                    'user_id'        => $user->id,
                    'payment_method' => $request->payment_method
                ]
            ]);

            if ($request->payment_method === 'stripe') {
                return $this->initiateStripeGiftCardPayment($package, $user);
            } 
            
            return $this->initiatePayPalGiftCardPayment($package, $user);
        });
    }

    private function initiateStripeGiftCardPayment($package, $user)
    {
        $stripeCredential = Credential::where('gateway', 'Stripe')->first();

        if (!$stripeCredential || !$stripeCredential->client_secret) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe credentials not configured'
            ], 400);
        }

        $paymentId = session('active_payment_id');

        Stripe::setApiKey($stripeCredential->client_secret);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'GBP',
                    'product_data' => [
                        'name' => $package->name,
                        'description' => 'Gift Card Purchase',
                        'metadata' => [
                            'payment_record_id' => $paymentId
                        ]
                    ],
                    'unit_amount' => intval($package->amount * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('giftcard.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('giftcard.payment.cancel'),
            'customer_email' => $user->email,
            'metadata' => [
                'payment_id' => $paymentId,
                'type' => 'giftcard'
            ]
        ]);

        return response()->json([
            'success' => true,
            'redirectUrl' => $session->url
        ]);
    }

    private function initiatePayPalGiftCardPayment($package, $user)
    {
        $this->setPayPalConfig();

        $paymentId = session('active_payment_id');

        $provider = new \Srmklive\PayPal\Services\PayPal;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('giftcard.payment.success'),
                "cancel_url" => route('giftcard.payment.cancel'),
            ],
            "purchase_units" => [
                [
                    "reference_id" => "PYMT_" . $paymentId,
                    "amount" => [
                        "currency_code" => "GBP",
                        "value" => number_format((float)$package->amount, 2, '.', '')
                    ],
                    "description" => $package->name . ' - Gift Card',
                    "custom_id" => $paymentId
                ]
            ]
        ]);

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

        return response()->json([
            'success' => false,
            'message' => $response['message'] ?? 'Failed to create PayPal order'
        ], 500);
    }

    public function giftCardPaymentSuccess(Request $request)
    {
        $paymentId = session('active_payment_id');
        $checkoutData = session('giftcard_checkout');

        if (!$paymentId || !$checkoutData) {
            return redirect()->route('gift-cards')->with('error', 'Invalid payment session');
        }

        return DB::transaction(function () use ($request, $paymentId, $checkoutData) {
            $payment = Payment::findOrFail($paymentId);
            
            if ($payment->payment_method === 'paypal') {
                $this->handlePayPalGiftCardPayment($request, $checkoutData);
                $payment->transaction_id = $request->token;
            } else {
                $payment->transaction_id = $request->session_id;
            }

            $payment->status = 'completed';
            $payment->save();

            $giftCard = GiftCard::create([
                'code'         => $this->generateGiftCardCode(),
                'amount'       => $payment->amount,
                'balance'      => $payment->amount,
                'status'       => 'new',
                'is_active'    => true,
                'purchased_by' => $payment->user_id,
                'purchased_at' => now(),
                'expires_at' => now()->addYears(10)
            ]);

            $payment->update(['reference_id' => $giftCard->id]);

            session()->forget(['giftcard_checkout', 'active_payment_id']);

            return redirect()->route('gift-cards')->with('success', 'Gift card purchased successfully! Code: ' . $giftCard->code);
        });
    }

    private function handlePayPalGiftCardPayment(Request $request, $checkoutData)
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

        if (!isset($response['status']) || $response['status'] !== 'COMPLETED') {
            throw new \Exception($response['message'] ?? 'PayPal payment capture failed');
        }

        return $response; 
    }

    public function giftCardPaymentCancel()
    {
        $paymentId = session('active_payment_id');

        if ($paymentId) {
            // Mark the payment as failed/cancelled in your ledger
            Payment::where('id', $paymentId)
                    ->where('status', 'pending')
                    ->update(['status' => 'failed']);
        }

        session()->forget(['giftcard_checkout', 'active_payment_id']);
        
        return redirect()->route('gift-cards')->with('error', 'Payment was cancelled.');
    }

    private function generateGiftCardCode()
    {
        do {
            $code = 'GC' . rand(10000000000000, 99999999999999);
        } while (GiftCard::where('code', $code)->exists());

        return $code;
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

    public function checkout()
    {
        return view('frontend.checkout');
    }

    public function validatePromoCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|uppercase',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $code = $request->code;
        $subtotal = $request->subtotal;
        $userId = auth()->id();

        $giftCard = GiftCard::where('code', $code)->first();
        
        if ($giftCard) {
            if (!$giftCard->is_active) {
                return response()->json(['message' => 'This gift card is inactive'], 400);
            }
            if ($giftCard->status === 'expired') {
                return response()->json(['message' => 'This gift card has expired'], 400);
            }
            if ($giftCard->status === 'used') {
                return response()->json(['message' => 'This gift card has already been used'], 400);
            }
            if ($giftCard->expires_at && $giftCard->expires_at < now()) {
                return response()->json(['message' => 'This gift card has expired'], 400);
            }

            $discount_amount = min($giftCard->balance, $subtotal);

            return response()->json([
                'valid' => true,
                'type' => 'gift_card',
                'discount_amount' => (float) $discount_amount,
                'code_data' => [
                    'id' => $giftCard->id,
                    'code' => $giftCard->code,
                    'balance' => (float) $giftCard->balance
                ]
            ], 200);
        }

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json(['message' => 'Invalid coupon or gift card code'], 404);
        }

        if (!$coupon->is_active) {
            return response()->json(['message' => 'This coupon is inactive'], 400);
        }

        if ($coupon->start_date && $coupon->start_date > now()) {
            return response()->json(['message' => 'This coupon is not yet active'], 400);
        }

        if ($coupon->end_date && $coupon->end_date < now()) {
            return response()->json(['message' => 'This coupon has expired'], 400);
        }

        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            return response()->json(['message' => 'This coupon has reached its maximum usage limit'], 400);
        }

        // Check max uses per user
        if ($coupon->max_uses_per_user && $userId) {
            $userUsage = CouponUsage::where('coupon_id', $coupon->id)
                ->where('user_id', $userId)
                ->first();
            
            if ($userUsage && $userUsage->usage_count >= $coupon->max_uses_per_user) {
                return response()->json([
                    'message' => 'You have reached the maximum usage limit for this coupon'
                ], 400);
            }
        }

        if ($coupon->is_birthday_voucher && $userId) {
            $birthdayVoucherUsage = $coupon->users()
                ->where('user_id', $userId)
                ->first();
            
            if ($birthdayVoucherUsage && $birthdayVoucherUsage->pivot->used_count > 0) {
                return response()->json([
                    'message' => 'You have already used this birthday voucher'
                ], 400);
            }
        }

        if ($coupon->min_order_amount > 0 && $subtotal < $coupon->min_order_amount) {
            return response()->json([
                'message' => 'Minimum order amount of £' . number_format($coupon->min_order_amount, 2) . ' required'
            ], 400);
        }

        $discount_amount = 0;
        
        if ($coupon->discount_type === 'percent') {
            $discount_amount = ($subtotal * $coupon->discount_value) / 100;
        } else {
            $discount_amount = $coupon->discount_value;
        }

        return response()->json([
            'valid' => true,
            'type' => 'coupon',
            'discount_amount' => (float) $discount_amount,
            'code_data' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value
            ]
        ], 200);
    }

    public function placeOrder(Request $request)
    {
        if ($request->input('delivery.type') === 'delivery') {
            $request->validate([
                'address' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'delivery.postcode' => 'required|string|max:20',
            ]);
        }

        $request->validate([
            'customer.firstName' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'customer.lastName' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'customer.email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'customer.phone' => ['required', 'string', 'regex:/^(?:\+44\s?|0)[0-9\s]{9,11}$/'],
            'delivery.type' => 'required|in:delivery,collection',
            'delivery.time' => 'required|string',
            'cart' => 'required|array|min:1',
            'cart.*.productId' => 'required|integer|exists:products,id',
            'cart.*.quantity' => 'required|integer|min:1|max:999',
            'cart.*.type' => 'nullable|string',
            'cart.*.options' => 'nullable|array',
            'paymentMethod' => 'required|in:cash,stripe,paypal',
            'pointsToUse' => 'nullable|integer|min:0',
            'promoCode' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $customer = $request->input('customer');
        $delivery = $request->input('delivery');
        $cart = $request->input('cart');
        $paymentMethod = $request->input('paymentMethod');
        $pointsToUse = (int)($request->input('pointsToUse') ?? 0);
        $promoCode = $request->input('promoCode');
        $address = $request->input('address');
        $address2 = $request->input('address2');
        $city = $request->input('city');
        $notes = $request->input('notes');

        $subtotal = 0;
        $cartItems = [];

        foreach ($cart as $item) {
            $product = Product::find($item['productId']);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 400);
            }

            $itemPrice = $product->price;
            $optionPrice = 0;
            $attributePrice = 0;

            if (($item['attribute'] ?? false) && $product->has_attribute) {
                $attributePrice = (float)$product->attribute_price;
            }

            if ($item['type'] === 'custom' && !empty($item['options'])) {
                foreach ($item['options'] as $optionName => $optionValues) {
                    foreach ($optionValues as $opt) {
                        $optionItem = ProductOptionItem::where('hubrise_option_ref', $opt['hubriseOptionRef'])
                            ->first();
                        
                        if ($optionItem) {
                            $optionPrice += $optionItem->override_price;
                        }
                    }
                }
            }

            $unitPrice = $itemPrice + $optionPrice + $attributePrice;
            $totalItemPrice = $unitPrice * (int)$item['quantity'];
            $subtotal += $totalItemPrice;

            $cartItems[] = [
                'productId' => $item['productId'],
                'product' => $product,
                'quantity' => $item['quantity'],
                'basePrice' => $itemPrice,
                'optionPrice' => $optionPrice,
                'unitPrice' => $itemPrice + $optionPrice + $attributePrice,
                'totalPrice' => $totalItemPrice,
                'options' => $item['options'] ?? []
            ];
        }

        $subtotal = round($subtotal, 2);

        $deliveryCharge = 0;
        if ($delivery['type'] === 'delivery') {
            $user = auth()->user();
            
            if ($user && $user->hasActiveDeliverySubscription()) {
                $deliveryCharge = 0.00;
            } else {
                $postcode = $delivery['postcode'];
                $deliveryData = $this->getDeliveryCharge($postcode);
                
                if (!$deliveryData['available']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Delivery not available for this postcode'
                    ], 400);
                }

                $deliveryCharge = $deliveryData['charge'];
            }
        }

        $deliveryCharge = round($deliveryCharge, 2);

        $promoDiscount = 0;
        $promoType = null;
        $promoId = null;

        if ($promoCode) {
            $promoValidation = $this->validatePromoCodeBackend($promoCode, $subtotal);
            
            if (!$promoValidation['valid']) {
                return response()->json([
                    'success' => false,
                    'message' => $promoValidation['message']
                ], 400);
            }

            $promoDiscount = $promoValidation['discount_amount'];
            $promoType = $promoValidation['type'];
            $promoId = $promoValidation['code_data']['id'];
        }

        $promoDiscount = round($promoDiscount, 2);

        $pointsDiscount = 0;
        if ($pointsToUse > 0) {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Must be logged in to use points'
                ], 400);
            }

            $userPoints = auth()->user()->available_points ?? 0;
            
            if ($pointsToUse > $userPoints) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient points'
                ], 400);
            }

            $pointsDiscount = round($pointsToUse / 100, 2);
            
            $remainingTotal = $subtotal + $deliveryCharge - $promoDiscount;
            if ($pointsDiscount > $remainingTotal) {
                $pointsDiscount = $remainingTotal;
            }
        }

        $pointsDiscount = round($pointsDiscount, 2);

        $total = round($subtotal + $deliveryCharge - $promoDiscount - $pointsDiscount, 2);

        if ($total < 0) {
            $total = 0;
        }

        $calculationData = [
            'subtotal' => $subtotal,
            'deliveryCharge' => $deliveryCharge,
            'promoDiscount' => $promoDiscount,
            'promoType' => $promoType,
            'promoId' => $promoId,
            'pointsToUse' => $pointsToUse,
            'pointsDiscount' => $pointsDiscount,
            'total' => $total,
            'cartItems' => $cartItems,
            'customer' => $customer,
            'delivery' => $delivery,
            'address' => $address,
            'address2' => $address2,
            'city' => $city,
            'paymentMethod' => $paymentMethod,
            'notes' => $notes
        ];

        if ($paymentMethod === 'cash') {
            return $this->processCashOnDelivery($calculationData);
        } elseif ($paymentMethod === 'stripe') {
            return $this->processStripePayment($calculationData);
        } elseif ($paymentMethod === 'paypal') {
            return $this->processPayPalPayment($calculationData);
        }
    }

    private function processStripePayment($calculationData)
    {
        try {
            $stripeCredential = Credential::where('gateway', 'Stripe')->first();

            if (!$stripeCredential || !$stripeCredential->client_secret) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe credentials not configured'
                ], 400);
            }

            $payment = Payment::create([
                'user_id' => auth()->id(),
                'payment_type' => 'order',
                'reference_id' => 0,
                'amount' => $calculationData['total'],
                'currency' => 'GBP',
                'payment_method' => 'stripe',
                'status' => 'pending',
                'metadata' => json_encode($calculationData)
            ]);

            session([
                'active_payment_id' => $payment->id,
                'checkout_data' => $calculationData
            ]);

            Stripe::setApiKey($stripeCredential->client_secret);

            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'GBP',
                        'product_data' => [
                            'name' => 'Food Order',
                            'description' => 'Restaurant Order'
                        ],
                        'unit_amount' => intval($calculationData['total'] * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('order.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('order.payment.cancel'),
                'customer_email' => $calculationData['customer']['email'],
                'metadata' => ['payment_id' => $payment->id]
            ]);

            $payment->update([
                'transaction_id' => $session->id
            ]);

            return response()->json([
                'success' => true,
                'redirectUrl' => $session->url
            ]);

        } catch (\Exception $e) {
            \Log::error('Stripe Payment Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment session'
            ], 400);
        }
    }

    private function processPayPalPayment($calculationData)
    {
        try {
            $payment = Payment::create([
                'user_id' => auth()->id(),
                'payment_type' => 'order',
                'reference_id' => 0,
                'amount' => $calculationData['total'],
                'currency' => 'GBP',
                'payment_method' => 'paypal',
                'status' => 'pending',
                'metadata' => json_encode($calculationData)
            ]);

            session([
                'active_payment_id' => $payment->id,
                'checkout_data' => $calculationData
            ]);

            $this->setPayPalConfig();

            $provider = new \Srmklive\PayPal\Services\PayPal;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('order.payment.success'),
                    "cancel_url" => route('order.payment.cancel'),
                ],
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "GBP",
                            "value" => number_format($calculationData['total'], 2, '.', '')
                        ]
                    ]
                ]
            ]);

            if (!isset($response['id']) || $response['id'] == null) {
                throw new \Exception($response['message'] ?? 'Failed to create PayPal order');
            }

            $payment->update([
                'transaction_id' => $response['id']
            ]);

            foreach ($response['links'] as $link) {
                if ($link['rel'] == 'approve') {
                    return response()->json([
                        'success' => true,
                        'redirectUrl' => $link['href']
                    ]);
                }
            }

            throw new \Exception('PayPal approval link not found');

        } catch (\Exception $e) {
            \Log::error('PayPal Payment Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'PayPal error: ' . $e->getMessage()
            ], 400);
        }
    }

    public function orderPaymentSuccess(Request $request)
    {
        $paymentId = session('active_payment_id');
        $calculationData = session('checkout_data');

        if (!$paymentId || !$calculationData) {
            return view('frontend.payment-error', ['message' => 'Invalid payment session']);
        }

        $payment = Payment::find($paymentId);
        
        if (!$payment) {
            return view('frontend.payment-error', ['message' => 'Payment not found']);
        }

        try {
            if ($payment->payment_method === 'stripe') {
                $sessionId = $request->input('session_id');
                if (!$sessionId) {
                    throw new \Exception('Session ID missing');
                }
                $this->handleStripeOrderPayment($payment, $sessionId);
            } elseif ($payment->payment_method === 'paypal') {
                $token = $request->input('token');
                $this->handlePayPalOrderPayment($payment, $token);
            }

            $payment->update(['status' => 'completed']);
            $order = $this->createOrder($calculationData);
            $this->sendToHubRise($order, $calculationData);
            $payment->update(['reference_id' => $order->id]);
            session()->forget(['active_payment_id', 'checkout_data']);

            return view('frontend.payment-success', [
                'order' => $order,
                'orderNumber' => $order->order_number,
                'orderId' => $order->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Order Payment Success Error: ' . $e->getMessage());
            return view('frontend.payment-error', ['message' => $e->getMessage()]);
        }
    }

    public function orderPaymentCancel()
    {
        $paymentId = session('active_payment_id');
        
        if ($paymentId) {
            Payment::where('id', $paymentId)->update(['status' => 'failed']);
        }

        session()->forget(['active_payment_id', 'checkout_data']);
        
        return view('frontend.payment-cancelled');
    }

    private function handleStripeOrderPayment($payment, $sessionId)
    {
        $stripeCredential = Credential::where('gateway', 'Stripe')->first();

        if (!$stripeCredential || !$stripeCredential->client_secret) {
            throw new \Exception('Stripe credentials not configured');
        }

        Stripe::setApiKey($stripeCredential->client_secret);
        
        $session = Session::retrieve($sessionId);
        
        if ($session->payment_status !== 'paid') {
            throw new \Exception('Stripe payment not confirmed');
        }
    }

    private function handlePayPalOrderPayment($payment, $token)
    {
        if (!$token) {
            throw new \Exception('PayPal token missing');
        }

        $this->setPayPalConfig();

        $provider = new \Srmklive\PayPal\Services\PayPal;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        
        $response = $provider->capturePaymentOrder($token);

        if (!isset($response['status']) || $response['status'] !== 'COMPLETED') {
            throw new \Exception($response['message'] ?? 'PayPal payment capture failed');
        }
    }

    private function processCashOnDelivery($calculationData)
    {
        try {
            $order = $this->createOrder($calculationData);

            $this->sendToHubRise($order, $calculationData);

            return response()->json([
                'success' => true,
                'orderNumber' => $order->order_number,
                'orderId' => $order->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Cash Order Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error placing order: ' . $e->getMessage()
            ], 400);
        }
    }

    private function createOrder($calculationData)
    {
        return DB::transaction(function () use ($calculationData) {
            $orderNumber = 'ORD-' . time() . '-' . rand(1000, 9999);
            $user = auth()->user();

            $order = Order::create([
                'user_id' => $user ? $user->id : null,
                'order_number' => $orderNumber,
                'customer_type' => $user ? 'authenticated' : 'guest',
                'first_name' => $calculationData['customer']['firstName'],
                'last_name' => $calculationData['customer']['lastName'],
                'email' => $calculationData['customer']['email'],
                'phone' => $calculationData['customer']['phone'],
                'address_1' => $calculationData['address'],
                'address_2' => $calculationData['address2'],
                'city' => $calculationData['city'],
                'postcode' => $calculationData['delivery']['postcode'] ?? null,
                'delivery_type' => $calculationData['delivery']['type'],
                'time' => $calculationData['delivery']['time'],
                'subtotal' => $calculationData['subtotal'],
                'delivery_charge' => $calculationData['deliveryCharge'],
                'coupon_discount' => $calculationData['promoType'] === 'coupon' ? $calculationData['promoDiscount'] : 0,
                'coupon_id' => $calculationData['promoType'] === 'coupon' ? $calculationData['promoId'] : null,
                'gift_card_discount' => $calculationData['promoType'] === 'gift_card' ? $calculationData['promoDiscount'] : 0,
                'gift_card_id' => $calculationData['promoType'] === 'gift_card' ? $calculationData['promoId'] : null,
                'points_used' => $calculationData['pointsToUse'] / 100,
                'total' => $calculationData['total'],
                'payment_method' => $calculationData['paymentMethod'],
                'payment_status' => 'pending',
                'status' => 'pending',
                'notes' => $calculationData['notes'],
                'hubrise_order_id' => null,
                'payment_transaction_id' => null
            ]);

            if ($user) {
                $user->increment('total_orders');
                $user->update(['last_order_date' => now()]);
            }

            foreach ($calculationData['cartItems'] as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['productId'],
                    'product_name' => $item['product']->title,
                    'sku_ref' => $item['product']->sku_ref,
                    'quantity' => $item['quantity'],
                    'price' => $item['unitPrice'],
                    'total' => $item['totalPrice']
                ]);

                if (!empty($item['options'])) {
                    foreach ($item['options'] as $optionName => $optionValues) {
                        foreach ($optionValues as $opt) {
                            OrderItemOption::create([
                                'order_item_id' => $orderItem->id,
                                'option_list_name' => $optionName,
                                'option_name' => $opt['title'],
                                'option_ref' => $opt['hubriseOptionRef'] ?? null,
                                'price' => $opt['price'] ?? 0
                            ]);
                        }
                    }
                }
            }

            return $order;
        });
    }

    private function sendToHubRise($order, $calculationData)
    {
        $accessToken = env('HUBRISE_ACCESS_TOKEN');
        $locationId = env('HUBRISE_LOCATION_ID');

        if (!$accessToken || !$locationId) {
            throw new \Exception('HubRise credentials not configured');
        }

        $items = [];
        foreach ($calculationData['cartItems'] as $item) {
            $itemData = [
                'product_name' => $item['product']->title,
                'sku_ref' => $item['product']->sku_ref,
                'price' => number_format($item['unitPrice'] + ($item['attributePrice'] ?? 0), 2, '.', '') . ' GBP',
                'quantity' => $item['quantity'],
                'options' => []
            ];

            if (!empty($item['options'])) {
                foreach ($item['options'] as $optionName => $optionValues) {
                    foreach ($optionValues as $opt) {
                        $itemData['options'][] = [
                            'option_list_name' => 'Option',
                            'name' => $opt['title'],
                            'ref' => (string)($opt['hubriseOptionRef'] ?? ''),
                            'price' => '0.00 GBP'
                        ];
                    }
                }
            }

            $items[] = $itemData;
        }

        $paymentRef = 'CASH';
        $paymentName = 'Cash on Delivery';
        
        if ($calculationData['paymentMethod'] === 'stripe') {
            $paymentRef = 'STRIPE';
            $paymentName = 'Stripe';
        } elseif ($calculationData['paymentMethod'] === 'paypal') {
            $paymentRef = 'PAYPAL';
            $paymentName = 'PayPal';
        }

        $hubRisePayload = [
            'status' => 'new',
            'channel' => 'Website',
            'service_type' => $calculationData['delivery']['type'],
            'service_type_ref' => $calculationData['delivery']['type'] === 'delivery' ? '9' : '10',
            'items' => $items,
            'payments' => [
                [
                    'name' => $paymentName,
                    'ref' => $paymentRef,
                    'amount' => number_format($calculationData['total'], 2, '.', '') . ' GBP'
                ]
            ],
            'customer' => [
                'first_name' => $calculationData['customer']['firstName'],
                'last_name' => $calculationData['customer']['lastName'],
                'email' => $calculationData['customer']['email'],
                'phone' => $calculationData['customer']['phone'],
                'address_1' => $calculationData['address'],
                'address_2' => $calculationData['address2'],
                'city' => $calculationData['city'],
                'postal_code' => $calculationData['delivery']['postcode'] ?? null
            ]
        ];

        if ($calculationData['notes']) {
            $hubRisePayload['customer_notes'] = $calculationData['notes'];
        }

        if ($calculationData['deliveryCharge'] > 0) {
            $hubRisePayload['charges'] = [
                [
                    'name' => 'Delivery',
                    'price' => number_format($calculationData['deliveryCharge'], 2, '.', '') . ' GBP'
                ]
            ];
        }

        if ($calculationData['promoType'] === 'coupon' && $calculationData['promoDiscount'] > 0) {
            $hubRisePayload['discounts'] = [
                [
                    'name' => 'Coupon',
                    'price_off' => number_format($calculationData['promoDiscount'], 2, '.', '') . ' GBP'
                ]
            ];
        } elseif ($calculationData['promoType'] === 'gift_card' && $calculationData['promoDiscount'] > 0) {
            $hubRisePayload['discounts'] = [
                [
                    'name' => 'Gift Card',
                    'price_off' => number_format($calculationData['promoDiscount'], 2, '.', '') . ' GBP'
                ]
            ];
        }

        if ($calculationData['pointsDiscount'] > 0) {
            if (!isset($hubRisePayload['discounts'])) {
                $hubRisePayload['discounts'] = [];
            }
            $hubRisePayload['discounts'][] = [
                'name' => 'Points Redeemed',
                'price_off' => number_format($calculationData['pointsDiscount'], 2, '.', '') . ' GBP'
            ];
        }

        $response = Http::withHeaders([
            'X-Access-Token' => $accessToken,
            'Content-Type' => 'application/json'
        ])->post(
            "https://api.hubrise.com/v1/locations/{$locationId}/orders",
            $hubRisePayload
        );

        if (!$response->successful()) {
            throw new \Exception('Failed to create order in HubRise: ' . $response->body());
        }

        $hubRiseData = $response->json();
        $hubRiseOrderId = $hubRiseData['id'] ?? null;

        $order->update([
            'hubrise_order_id' => $hubRiseOrderId,
            'status' => 'pending'
        ]);

        $this->allocateOrderResources($order, $calculationData);

        $this->sendOrderConfirmationEmail($order);
    }

    private function allocateOrderResources($order, $calculationData)
    {
        if ($calculationData['promoType'] === 'gift_card' && $calculationData['promoId']) {
            $giftCard = GiftCard::find($calculationData['promoId']);
            if ($giftCard) {
                $newBalance = $giftCard->balance - $calculationData['promoDiscount'];
                
                $giftCard->update([
                    'balance' => $newBalance,
                    'status' => $newBalance <= 0 ? 'used' : 'new',
                    'order_id' => $order->id,
                    'redeemed_by' => auth()->id(),
                    'redeemed_at' => now()
                ]);
            }
        }

        if ($order->coupon_id) {
            $coupon = Coupon::find($order->coupon_id);
            if ($coupon) {
                $coupon->increment('used_count');
                
                if ($coupon->is_birthday_voucher && $order->user_id) {
                    $coupon->users()->updateExistingPivot($order->user_id, [
                        'used_count' => 1
                    ]);
                } elseif ($order->user_id) {
                    CouponUsage::updateOrCreate(
                        [
                            'coupon_id' => $coupon->id,
                            'user_id' => $order->user_id
                        ],
                        [
                            'usage_count' => \DB::raw('usage_count + 1')
                        ]
                    );
                }
            }
        }

        if ($order->user_id && $calculationData['pointsToUse'] > 0) {
            UserPoint::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'point' => -$calculationData['pointsToUse']
            ]);
        }

        if ($order->user_id) {
            $pointsEarned = floor($order->total);
            
            $orderCount = Order::where('user_id', $order->user_id)->count();
            if ($orderCount === 1) {
                $pointsEarned += 500;
                
                $user = User::find($order->user_id);
                if ($user->referred_by) {
                    $referrer = User::find($user->referred_by);
                    UserPoint::create([
                        'user_id' => $referrer->id,
                        'order_id' => $order->id,
                        'source' => 'referral_first_order_bonus',
                        'point' => 100,
                        'description' => 'First order bonus from referral: ' . $user->name,
                    ]);
                }
            }
            
            UserPoint::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'point' => $pointsEarned
            ]);
        }
    }

    private function getDeliveryCharge($postcode)
    {
        $centerLatitude = 53.223912;
        $centerLongitude = -0.532985;
        $deliveryRadius = 7.5;

        try {
            $response = Http::get('https://api.postcodes.io/postcodes/' . $postcode);
            
            if (!$response->successful()) {
                return ['available' => false];
            }

            $data = $response->json();
            $latitude = $data['result']['latitude'];
            $longitude = $data['result']['longitude'];

            $lat1Rad = deg2rad($centerLatitude);
            $lon1Rad = deg2rad($centerLongitude);
            $lat2Rad = deg2rad($latitude);
            $lon2Rad = deg2rad($longitude);

            $dlat = $lat2Rad - $lat1Rad;
            $dlon = $lon2Rad - $lon1Rad;

            $a = sin($dlat / 2) ** 2 +
                cos($lat1Rad) * cos($lat2Rad) *
                sin($dlon / 2) ** 2;

            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distance = 3959 * $c;

            if ($distance <= $deliveryRadius) {
                $charge = 2.00;
                if ($distance > 4) {
                    $charge = 3.00;
                }

                return [
                    'available' => true,
                    'charge' => $charge,
                    'distance' => $distance
                ];
            }

            return ['available' => false];

        } catch (\Exception $e) {
            return ['available' => false];
        }
    }

    private function validatePromoCodeBackend($code, $subtotal)
    {
        $giftCard = GiftCard::where('code', strtoupper($code))->first();
        
        if ($giftCard) {
            if (!$giftCard->is_active) {
                return ['valid' => false, 'message' => 'This gift card is inactive'];
            }
            if ($giftCard->status === 'used') {
                return ['valid' => false, 'message' => 'This gift card has already been used'];
            }
            if ($giftCard->expires_at && $giftCard->expires_at < now()) {
                return ['valid' => false, 'message' => 'This gift card has expired'];
            }

            $discount = min($giftCard->balance, $subtotal);

            return [
                'valid' => true,
                'type' => 'gift_card',
                'discount_amount' => $discount,
                'code_data' => ['id' => $giftCard->id, 'code' => $giftCard->code]
            ];
        }

        $coupon = Coupon::where('code', strtoupper($code))->first();

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid coupon or gift card code'];
        }

        if (!$coupon->is_active) {
            return ['valid' => false, 'message' => 'This coupon is inactive'];
        }

        if ($coupon->end_date && $coupon->end_date < now()) {
            return ['valid' => false, 'message' => 'This coupon has expired'];
        }

        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            return ['valid' => false, 'message' => 'This coupon has reached its maximum usage limit'];
        }

        if ($coupon->min_order_amount > 0 && $subtotal < $coupon->min_order_amount) {
            return ['valid' => false, 'message' => 'Minimum order amount of £' . number_format($coupon->min_order_amount, 2) . ' required'];
        }

        $discount = 0;
        if ($coupon->discount_type === 'percent') {
            $discount = ($subtotal * $coupon->discount_value) / 100;
        } else {
            $discount = $coupon->discount_value;
        }

        return [
            'valid' => true,
            'type' => 'coupon',
            'discount_amount' => $discount,
            'code_data' => ['id' => $coupon->id, 'code' => $coupon->code, 'discount_type' => $coupon->discount_type, 'discount_value' => $coupon->discount_value]
        ];
    }

    public function orderConfirmation($orderNumber)
    {
        $order = Order::with(['items.options'])->where('order_number', $orderNumber)->first();

        if (!$order) {
            return redirect('/')->with('error', 'Order not found');
        }

        return view('frontend.order-confirmation', compact('order'));
    }

    private function sendOrderConfirmationEmail($order)
    {
        try {
            Mail::to($order->email)->send(new OrderConfirmationMail($order));
        } catch (\Exception $e) {
        }
    }

    public function setupHubRiseCallback()
    {
        try {
            $accessToken = env('HUBRISE_ACCESS_TOKEN');

            if (!$accessToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'HUBRISE_ACCESS_TOKEN not configured'
                ], 500);
            }

            $response = Http::withHeaders([
                'X-Access-Token' => $accessToken,
                'Content-Type' => 'application/json'
            ])->post(
                "https://api.hubrise.com/v1/callback",
                [
                    'url' => url('/hubrise-webhook'),
                    'events' => [
                        'order' => ['update']
                    ]
                ]
            );

            $data = $response->json();

            return response()->json([
                'success' => true,
                'message' => 'HubRise callback configured successfully',
                'webhook_url' => url('/hubrise-webhook'),
                'response' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error setting up callback: ' . $e->getMessage()
            ], 500);
        }
    }

    public function hubRiseOrderCallback(Request $request)
    {
        try {
            $data = $request->json()->all();

            $resourceType = $data['resource_type'] ?? null;
            $eventType = $data['event_type'] ?? null;
            $hubRiseOrderId = $data['order_id'] ?? null;
            $newState = $data['new_state'] ?? [];
            $orderStatus = $newState['status'] ?? null;

            if ($resourceType !== 'order' || $eventType !== 'update') {
                return response()->json(['success' => true], 200);
            }

            if (!$hubRiseOrderId || !$orderStatus) {
                return response()->json(['success' => true], 200);
            }

            $order = Order::where('hubrise_order_id', $hubRiseOrderId)->first();

            if (!$order) {
                return response()->json(['success' => true], 200);
            }

            $statusMap = [
                'new' => 'pending',
                'accepted' => 'confirmed',
                'in_preparation' => 'preparing',
                'ready' => 'ready',
                'in_delivery' => 'confirmed',
                'delivered' => 'delivered',
                'completed' => 'delivered',
                'cancelled' => 'cancelled',
                'rejected' => 'cancelled',
                'delivery_failed' => 'cancelled',
            ];

            $newLocalStatus = $statusMap[$orderStatus] ?? $orderStatus;
            $order->update(['status' => $newLocalStatus]);

            if (in_array($orderStatus, ['cancelled', 'rejected'])) {
                $this->reverseOrderResources($order);
            }

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            \Log::error('HubRise callback error: ' . $e->getMessage(), $request->all());
            return response()->json(['success' => false], 200);
        }
    }

    private function reverseOrderResources($order)
    {
        if ($order->gift_card_id) {
            $giftCard = GiftCard::find($order->gift_card_id);
            if ($giftCard) {
                $giftCard->update([
                    'balance' => $giftCard->balance + $order->gift_card_discount,
                    'status' => 'new',
                    'redeemed_by' => null,
                    'redeemed_at' => null,
                    'order_id' => null
                ]);
            }
        }

        if ($order->coupon_id && $order->user_id) {
            $coupon = Coupon::find($order->coupon_id);
            if ($coupon) {
                $coupon->update([
                    'used_count' => max(0, $coupon->used_count - 1)
                ]);

                if ($coupon->is_birthday_voucher) {
                    $coupon->users()->updateExistingPivot($order->user_id, [
                        'used_count' => 0
                    ]);
                } else {
                    CouponUsage::where([
                        'coupon_id' => $coupon->id,
                        'user_id' => $order->user_id
                    ])->decrement('usage_count');
                }
            }
        }

        if ($order->user_id) {
            if ($order->points_used > 0) {
                UserPoint::where('order_id', $order->id)
                    ->where('point', -$order->points_used)
                    ->delete();
            }

            UserPoint::where('order_id', $order->id)
                ->where('point', '>', 0)
                ->delete();
        }
    }

    public function findUs()
    {
        $menu = Master::firstOrCreate(['name' => 'find-us']);
        $this->seo(
            $menu->meta_title,
            $menu->meta_description,
            $menu->meta_keywords,
            $menu->meta_image ? asset('uploads/meta_image/' . $menu->meta_image) : null
        );
        return view('frontend.find-us');
    }

    public function contact()
    {
        $menu = Master::firstOrCreate(['name' => 'contact']);
        $this->seo(
            $menu->meta_title,
            $menu->meta_description,
            $menu->meta_keywords,
            $menu->meta_image ? asset('uploads/meta_image/' . $menu->meta_image) : null
        );
      return view('frontend.contact');
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|min:2|max:100',
            'email'   => 'required|email|max:50',
            'phone'   => ['required', 'regex:/^(?:\+44|0)(?:7\d{9}|1\d{9}|2\d{9}|3\d{9})$/'],
            'company' => 'nullable|string|max:100',
            'message' => 'required|string|max:2000',
        ]);

        $contact = new Contact();
        $contact->name    = $request->input('name');
        $contact->email   = $request->input('email');
        $contact->phone   = $request->input('phone');
        $contact->message = $request->input('message');
        $contact->save();

        $contactEmails = ContactEmail::where('status', 1)->pluck('email');

        foreach ($contactEmails as $contactEmail) {
            Mail::to($contactEmail)->send(new ContactMail($contact));
        }

        return back()->with('success', 'Your message has been sent successfully!');
    }
    
    public function privacyPolicy()
    {
        $companyPrivacy = CompanyDetails::select('privacy_policy')->first();
        return view('frontend.privacy', compact('companyPrivacy'));
    }

    public function termsAndConditions()
    {
        $terms = CompanyDetails::select('terms_and_conditions')->first();
        return view('frontend.terms', compact('terms'));
    }

    public function promotions()
    {
        $promotions = CompanyDetails::select('promotions_description')->first();
        return view('frontend.promotions', compact('promotions'));
    }

    private function seo($title = null, $description = null, $keywords = null, $image = null)
    {
        if ($title) {
            SEOMeta::setTitle($title);
            OpenGraph::setTitle($title);
            Twitter::setTitle($title);
        }

        if ($description) {
            SEOMeta::setDescription($description);
            OpenGraph::setDescription($description);
            Twitter::setDescription($description);
        }

        if ($keywords) {
            SEOMeta::setKeywords($keywords);
        }

        if ($image) {
            OpenGraph::addImage($image);
            Twitter::setImage($image);
        }

        OpenGraph::setUrl(url()->current());
        OpenGraph::setType('website');
        OpenGraph::setSiteName(config('app.name'));
        
        Twitter::setType('summary_large_image');
        
        SEOMeta::setRobots('index, follow');
        SEOMeta::setCanonical(url()->current());
    }

    public function sitemap()
    {
        $urls = [];

        $staticRoutes = [
            '/' => 1.0,
            '/menu' => 0.9,
            '/gift-cards' => 0.8,
            '/our-story' => 0.6,
            '/find-us' => 0.6,
            '/contact' => 0.5,
            '/privacy-policy' => 0.3,
            '/terms-and-conditions' => 0.3,
            '/login' => 0.2,
            '/register' => 0.2,
        ];

        foreach ($staticRoutes as $path => $priority) {
            $urls[] = [
                'loc' => url($path),
                'lastmod' => Carbon::now()->toDateString(),
                'changefreq' => 'weekly',
                'priority' => $priority,
            ];
        }

        return response()
            ->view('frontend.sitemap', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }

    public function facebookCatalog()
    {
        $feed = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $feed .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
        $feed .= '<channel>' . "\n";
        $feed .= '<title>' . config('app.name') . ' - Product Catalog</title>' . "\n";
        $feed .= '<link>' . url('/') . '</link>' . "\n";
        $feed .= '<description>Product Catalog Feed for Facebook</description>' . "\n";

        $products = Product::where('status', 1)->get();

        foreach ($products as $product) {
            $feed .= '<item>' . "\n";
            $feed .= '<title>' . htmlspecialchars($product->title) . '</title>' . "\n";
            $feed .= '<description>' . htmlspecialchars(strip_tags($product->short_description)) . '</description>' . "\n";
            $feed .= '<g:link>' . htmlspecialchars(route('product.details', $product->slug)) . '</g:link>' . "\n";
            $feed .= '<g:image_link>' . htmlspecialchars(asset($product->image)) . '</g:image_link>' . "\n";
            $feed .= '<g:price>' . number_format($product->price, 2) . ' GBP</g:price>' . "\n";
            $feed .= '<g:availability>' . ($product->stock_status === 'in_stock' ? 'in stock' : 'out of stock') . '</g:availability>' . "\n";
            $feed .= '</item>' . "\n";
        }

        $feed .= '</channel>' . "\n";
        $feed .= '</rss>';

        return response($feed, 200, [
            'Content-Type' => 'application/xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
