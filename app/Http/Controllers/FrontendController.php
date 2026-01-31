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
            'amount' => 'required|numeric',
            'payment_method' => 'required|in:stripe,paypal'
        ]);

        $package = GiftcardPackage::findOrFail($request->package_id);
        $user = auth()->user();
        $paymentMethod = $request->payment_method;

        try {
            if ($paymentMethod === 'stripe') {
                return $this->initiateStripeGiftCardPayment($package, $user);
            } elseif ($paymentMethod === 'paypal') {
                return $this->initiatePayPalGiftCardPayment($package, $user);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid payment method'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
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

        session([
            'giftcard_checkout' => [
                'package_id' => $package->id,
                'amount' => $package->amount,
                'user_id' => $user->id,
                'payment_method' => 'stripe'
            ]
        ]);

        Stripe::setApiKey($stripeCredential->client_secret);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'GBP',
                    'product_data' => [
                        'name' => $package->name,
                        'description' => 'Gift Card - £' . number_format($package->amount, 2)
                    ],
                    'unit_amount' => intval($package->amount * 100),
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('giftcard.payment.success'),
            'cancel_url' => route('giftcard.payment.cancel'),
            'customer_email' => $user->email,
        ]);

        return response()->json([
            'success' => true,
            'redirectUrl' => $session->url
        ]);
    }

    private function initiatePayPalGiftCardPayment($package, $user)
    {
        try {
            $this->setPayPalConfig();

            session([
                'giftcard_checkout' => [
                    'package_id' => $package->id,
                    'amount' => $package->amount,
                    'user_id' => $user->id,
                    'payment_method' => 'paypal'
                ]
            ]);

            $provider = new \Srmklive\PayPal\Services\PayPal;
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('giftcard.payment.success'),
                    "cancel_url" => route('giftcard.payment.cancel'),
                ],
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "GBP",
                            "value" => number_format((float)$package->amount, 2, '.', '')
                        ],
                        "description" => $package->name . ' - Gift Card'
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

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'PayPal error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function giftCardPaymentSuccess(Request $request)
    {
        $checkoutData = session('giftcard_checkout');

        if (!$checkoutData) {
            return redirect()->route('gift-cards')->with('error', 'Invalid payment session');
        }

        try {
            $paymentMethod = $checkoutData['payment_method'] ?? 'stripe';

            if ($paymentMethod === 'paypal') {
                $this->handlePayPalGiftCardPayment($request, $checkoutData);
            }

            $package = GiftcardPackage::find($checkoutData['package_id']);

            $giftCard = GiftCard::create([
                'code' => $this->generateGiftCardCode(),
                'amount' => $package->amount,
                'balance' => $package->amount,
                'status' => 'new',
                'is_active' => true,
                'purchased_by' => $checkoutData['user_id'],
                'purchased_at' => now(),
                'expires_at' => now()->addYear()
            ]);

            session()->forget('giftcard_checkout');

            return redirect()->route('gift-cards')->with('success', 'Gift card purchased successfully! Code: ' . $giftCard->code);

        } catch (\Exception $e) {
            return redirect()->route('gift-cards')->with('error', 'Error processing gift card: ' . $e->getMessage());
        }
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
    }

    public function giftCardPaymentCancel()
    {
        session()->forget('giftcard_checkout');
        return redirect()->route('gift-cards')->with('error', 'Payment cancelled');
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
        if (($request->input('hubRiseOrder.service_type') ?? '') === 'delivery') {
            $request->validate([
                'localOrder.address' => 'required|string|max:255',
                'localOrder.city' => 'required|string|max:100',
                'localOrder.postalCode' => 'required|string|max:20',
            ], [
                'localOrder.address.required' => 'Address is required',
                'localOrder.city.required' => 'City is required',
                'localOrder.postalCode.required' => 'Postcode is required',
            ]);
        }

        $validated = $request->validate([
            'hubRiseOrder' => 'required|array',
            'hubRiseOrder.service_type' => 'required|in:delivery,collection',

            'localOrder' => 'required|array',
            'localOrder.customer.firstName' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'localOrder.customer.lastName' => ['required', 'string', 'max:100', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'localOrder.customer.email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'localOrder.customer.phone' => ['required', 'string', 'regex:/^(?:\+44\s?|0)[0-9\s]{9,11}$/'],

            'localOrder.delivery' => 'required|array',
            'localOrder.delivery.type' => 'required|in:delivery,collection',
            'localOrder.delivery.time' => 'required|string',
            'localOrder.delivery.charge' => 'required|numeric',

            'localOrder.cart' => 'required|array|min:1',
            'localOrder.cart.*.title' => ['required', 'string', 'max:255'],
            'localOrder.cart.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'localOrder.cart.*.price' => ['required', 'numeric', 'min:0', 'max:99999.99'],

            'localOrder.subtotal' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'localOrder.deliveryCharge' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'localOrder.total' => ['required', 'numeric', 'min:0', 'max:999999.99'],

            'localOrder.paymentMethod' => 'required|in:cash,stripe,paypal',
            'localOrder.points_used' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'localOrder.promo_type' => 'nullable|in:coupon,gift_card',
            'localOrder.promo_id' => ['nullable', 'integer', 'min:1'],
            'localOrder.promo_discount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ], [
            'localOrder.customer.firstName.required' => 'Customer first name is required',
            'localOrder.customer.firstName.regex' => 'First name can only contain letters, spaces, hyphens and apostrophes',

            'localOrder.customer.lastName.required' => 'Customer last name is required',
            'localOrder.customer.lastName.regex' => 'Last name can only contain letters, spaces, hyphens and apostrophes',

            'localOrder.customer.email.required' => 'Customer email is required',
            'localOrder.customer.email.email' => 'Please enter a valid email address',

            'localOrder.customer.phone.required' => 'Customer phone number is required',
            'localOrder.customer.phone.regex' => 'Please enter a valid UK phone number',

            'localOrder.delivery.required' => 'Delivery information is required',
            'localOrder.delivery.type.required' => 'Delivery type is required',
            'localOrder.delivery.time.required' => 'Delivery time is required',
            'localOrder.delivery.charge.required' => 'Delivery charge is required',

            'localOrder.cart.required' => 'Cart items are required',
            'localOrder.cart.min' => 'Cart must contain at least one item',

            'localOrder.subtotal.required' => 'Subtotal is required',
            'localOrder.deliveryCharge.required' => 'Delivery charge is required',
            'localOrder.total.required' => 'Total amount is required',

            'localOrder.paymentMethod.required' => 'Payment method is required',
        ]);

        $hubRiseOrder = $request->input('hubRiseOrder');
        $localOrder = $validated['localOrder'];

        $accessToken = env('HUBRISE_ACCESS_TOKEN');
        $locationId = env('HUBRISE_LOCATION_ID');

        if (!$accessToken || !$locationId) {
            return response()->json([
                'success' => false,
                'message' => 'HubRise credentials not configured'
            ], 500);
        }

        $serviceType = $hubRiseOrder['service_type'] ?? 'delivery';
        $paymentRefMap = ['cash' => 'CASH', 'stripe' => 'STRIPE', 'paypal' => 'PAYPAL'];
        $orderNumber = 'ORD-' . time() . '-' . rand(1000, 9999);

        $order = Order::create([
            'user_id' => auth()->id(),
            'order_number' => $orderNumber,
            'customer_type' => auth()->check() ? 'authenticated' : 'guest',
            'first_name' => $localOrder['customer']['firstName'],
            'last_name' => $localOrder['customer']['lastName'],
            'email' => $localOrder['customer']['email'],
            'phone' => $localOrder['customer']['phone'],
            'address_1' => $localOrder['address'] ?? null,
            'address_2' => $localOrder['address2'] ?? null,
            'street' => $localOrder['address'] ?? null,
            'city' => $localOrder['city'] ?? null,
            'postcode' => $localOrder['postalCode'] ?? null,
            'delivery_type' => $localOrder['delivery']['type'],
            'time' => $localOrder['delivery']['time'],
            'subtotal' => $localOrder['subtotal'],
            'delivery_charge' => $localOrder['deliveryCharge'],
            'coupon_discount' => ($localOrder['promo_type'] ?? null) === 'coupon' ? ($localOrder['promo_discount'] ?? 0) : 0,
            'coupon_id' => ($localOrder['promo_type'] ?? null) === 'coupon' ? ($localOrder['promo_id'] ?? null) : null,
            'gift_card_discount' => ($localOrder['promo_type'] ?? null) === 'gift_card' ? ($localOrder['promo_discount'] ?? 0) : 0,
            'gift_card_id' => ($localOrder['promo_type'] ?? null) === 'gift_card' ? ($localOrder['promo_id'] ?? null) : null,
            'points_used' => intval($localOrder['points_used'] ?? 0) / 100,
            'total' => $localOrder['total'],
            'payment_method' => $localOrder['paymentMethod'],
            'payment_status' => 'pending',
            'status' => 'pending',
            'notes' => $localOrder['orderNotes'] ?? null,
            'hubrise_order_id' => null,
            'payment_transaction_id' => null
        ]);

        foreach ($localOrder['cart'] as $item) {
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['productId'] ?? null,
                'product_name' => $item['title'],
                'sku_ref' => $item['skuRef'] ?? null,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity']
            ]);

            if (($item['type'] ?? null) === 'custom' && !empty($item['options'])) {
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

        if (($localOrder['promo_type'] ?? null) === 'gift_card' && ($localOrder['promo_id'] ?? null)) {
            $giftCard = GiftCard::find($localOrder['promo_id']);
            if ($giftCard) {
                $newBalance = $giftCard->balance - ($localOrder['promo_discount'] ?? 0);
                
                $giftCard->update([
                    'balance' => $newBalance,
                    'status' => $newBalance <= 0 ? 'used' : 'new',
                    'order_id' => $order->id,
                    'redeemed_by' => auth()->id(),
                    'redeemed_at' => now()
                ]);
            }
        }

        if (($localOrder['paymentMethod'] ?? null) === 'stripe') {
            return $this->initiateStripePayment($order, $hubRiseOrder, $localOrder, $accessToken, $locationId, $paymentRefMap);
        } elseif (($localOrder['paymentMethod'] ?? null) === 'paypal') {
            return $this->initiatePayPalPayment($order, $hubRiseOrder, $localOrder, $accessToken, $locationId, $paymentRefMap);
        } else {
            $this->sendToHubRise($order, $hubRiseOrder, $localOrder, $accessToken, $locationId, $paymentRefMap);

            return response()->json([
                'success' => true,
                'confirmUrl' => route('order.confirmation', ['orderNumber' => $order->order_number]),
                'orderId' => $order->id,
                'orderNumber' => $order->order_number
            ]);
        }
    }

    private function initiatePayPalPayment($order, $hubRiseOrder, $localOrder, $accessToken, $locationId, $paymentRefMap)
    {
        try {
            session([
                'checkout_data' => [
                    'order_id' => $order->id,
                    'hubRiseOrder' => $hubRiseOrder,
                    'localOrder' => $localOrder,
                    'accessToken' => $accessToken,
                    'locationId' => $locationId,
                    'paymentRefMap' => $paymentRefMap
                ]
            ]);

            $this->setPayPalConfig();

            $provider = new \Srmklive\PayPal\Services\PayPal;
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('payment.success', ['order_id' => $order->id]),
                    "cancel_url" => route('payment.cancel'),
                ],
                "purchase_units" => [
                    [
                        "amount" => [
                            "currency_code" => "GBP",
                            "value" => number_format((float)$localOrder['total'], 2, '.', '')
                        ]
                    ]
                ]
            ]);

            if (isset($response['id']) && $response['id'] != null) {
                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {
                        $order->update([
                            'payment_transaction_id' => $response['id']
                        ]);

                        return response()->json([
                            'success' => true,
                            'redirectUrl' => $links['href'],
                            'orderId' => $order->id,
                            'orderNumber' => $order->order_number
                        ]);
                    }
                }
            }

            return response()->json([
                'success' => false,
                'message' => $response['message'] ?? 'Failed to create PayPal order'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'PayPal error: ' . $e->getMessage()
            ], 500);
        }
    }

    private function initiateStripePayment($order, $hubRiseOrder, $localOrder, $accessToken, $locationId, $paymentRefMap)
    {
        try {
            $stripeCredential = Credential::where('gateway', 'Stripe')->first();

            if (!$stripeCredential || !$stripeCredential->client_secret) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe credentials not configured'
                ], 400);
            }
            session([
                'checkout_data' => [
                    'order_id' => $order->id,
                    'hubRiseOrder' => $hubRiseOrder,
                    'localOrder' => $localOrder,
                    'accessToken' => $accessToken,
                    'locationId' => $locationId,
                    'paymentRefMap' => $paymentRefMap
                ]
            ]);

            Stripe::setApiKey($stripeCredential->client_secret);

            // Create Stripe Checkout Session
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'GBP',
                        'product_data' => [
                            'name' => 'Order #' . $order->order_number,
                            'description' => 'Restaurant Order'
                        ],
                        'unit_amount' => intval($localOrder['total'] * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => route('payment.success', ['order_id' => $order->id]),
                'cancel_url' => route('payment.cancel'),
                'customer_email' => $order->email,
            ]);

            // Update order with Stripe session ID
            $order->update([
                'payment_transaction_id' => $session->id
            ]);

            return response()->json([
                'success' => true,
                'redirectUrl' => $session->url,
                'orderId' => $order->id,
                'orderNumber' => $order->order_number
            ]);

        } catch (\Exception $e) {
            \Log::error('Stripe payment error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment session'
            ], 500);
        }
    }

    private function sendToHubRise($order, $hubRiseOrder, $localOrder, $accessToken, $locationId, $paymentRefMap)
    {
        $hubRisePayload = [
            'status' => 'new',
            'channel' => 'Website',
            'service_type' => $hubRiseOrder['service_type'],
            'service_type_ref' => $hubRiseOrder['service_type'] === 'delivery' ? '9' : '10',
            'items' => [],
            'payments' => !empty($hubRiseOrder['payments']) ? [
                [
                    'name' => $hubRiseOrder['payments'][0]['name'] ?? 'Cash on Delivery',
                    'ref' => $paymentRefMap[$localOrder['paymentMethod']] ?? 'ONLINE PAYMENT',
                    'amount' => $hubRiseOrder['payments'][0]['amount']
                ]
            ] : [],
            'customer' => [
                'first_name' => $hubRiseOrder['customer']['first_name'],
                'last_name' => $hubRiseOrder['customer']['last_name'] ?? '',
                'email' => $hubRiseOrder['customer']['email'] ?? null,
                'phone' => $hubRiseOrder['customer']['phone'] ?? null,
                'address_1' => $hubRiseOrder['customer']['address_1'] ?? null,
                'address_2' => $hubRiseOrder['customer']['address_2'] ?? null,
                'city' => $hubRiseOrder['customer']['city'] ?? null,
                'postal_code' => $hubRiseOrder['customer']['postal_code'] ?? null
            ]
        ];

        // Add customer notes
        if (!empty($hubRiseOrder['notes'])) {
            $hubRisePayload['customer_notes'] = $hubRiseOrder['notes'];
        }

        // Add items
        foreach ($hubRiseOrder['items'] as $item) {
            $itemData = [
                'product_name' => $item['product_name'],
                'sku_ref' => $item['sku_ref'] ?? null,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'options' => []
            ];

            if (!empty($item['options'])) {
                foreach ($item['options'] as $option) {
                    $itemData['options'][] = [
                        'option_list_name' => $option['option_list_name'],
                        'name' => $option['name'],
                        'ref' => (string)($option['ref'] ?? null),
                        'price' => '0.00 GBP'
                    ];
                }
            }

            $hubRisePayload['items'][] = $itemData;
        }

        // Add discounts
        if (!empty($hubRiseOrder['discounts'])) {
            $hubRisePayload['discounts'] = [];
            foreach ($hubRiseOrder['discounts'] as $discount) {
                $hubRisePayload['discounts'][] = [
                    'name' => $discount['name'],
                    'ref' => $discount['ref'] ?? null,
                    'price_off' => $discount['price_off']
                ];
            }
        }

        // Add charges
        if (!empty($hubRiseOrder['charges'])) {
            $hubRisePayload['charges'] = [];
            foreach ($hubRiseOrder['charges'] as $charge) {
                $hubRisePayload['charges'][] = [
                    'name' => $charge['name'],
                    'price' => $charge['price']
                ];
            }
        }

        // Send to HubRise
        $response = Http::withHeaders([
            'X-Access-Token' => $accessToken,
            'Content-Type' => 'application/json'
        ])->post(
            "https://api.hubrise.com/v1/locations/{$locationId}/orders",
            $hubRisePayload
        );

        if (!$response->successful()) {
            throw new \Exception('Failed to create order in HubRise');
        }

        $hubRiseData = $response->json();
        $hubRiseOrderId = $hubRiseData['id'] ?? null;

        // Update order with HubRise ID
        $order->update([
            'hubrise_order_id' => $hubRiseOrderId,
            // 'hubrise_order_id' => str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT),
            'status' => 'pending'
        ]);

        $this->sendOrderConfirmationEmail($order);
    }
    
    public function paymentSuccess(Request $request)
    {
        $orderId = $request->input('order_id');
        $token = $request->input('token');
        $checkoutData = session('checkout_data');

        if (!$checkoutData || $checkoutData['order_id'] != $orderId) {
            return view('frontend.payment-error', ['message' => 'Invalid payment session']);
        }

        $order = Order::find($orderId);
        if (!$order) {
            return view('frontend.payment-error', ['message' => 'Order not found']);
        }

        try {
            if ($order->payment_method === 'stripe') {
                $this->handleStripePaymentSuccess($order);
            } elseif ($order->payment_method === 'paypal') {
                $this->handlePayPalPaymentSuccess($order, $token);
            }

            $order->update([
                'payment_status' => 'paid',
                'status' => 'pending'
            ]);

            $this->sendToHubRise(
                $order,
                $checkoutData['hubRiseOrder'],
                $checkoutData['localOrder'],
                $checkoutData['accessToken'],
                $checkoutData['locationId'],
                $checkoutData['paymentRefMap']
            );

            session()->forget('checkout_data');

            return view('frontend.payment-success', [
                'order' => $order,
                'orderNumber' => $order->order_number,
                'orderId' => $order->id
            ]);

        } catch (\Exception $e) {
            return view('frontend.payment-error', ['message' => $e->getMessage()]);
        }
    }

    private function handleStripePaymentSuccess($order)
    {
        $stripeCredential = Credential::where('gateway', 'Stripe')->first();

        if (!$stripeCredential || !$stripeCredential->client_secret) {
            throw new \Exception('Stripe credentials not configured');
        }

        Stripe::setApiKey($stripeCredential->client_secret);
        $session = Session::retrieve($order->payment_transaction_id);
        
        if ($session->payment_status !== 'paid') {
            throw new \Exception('Stripe payment not confirmed');
        }
    }

    private function handlePayPalPaymentSuccess($order, $token)
    {
        $this->setPayPalConfig();
        $provider = new \Srmklive\PayPal\Services\PayPal;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($token);

        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            return true;
        } else {
            throw new \Exception($response['message'] ?? 'Payment capture failed');
        }
    }

    public function paymentCancel()
    {
        $checkoutData = session('checkout_data');
        
        if ($checkoutData && isset($checkoutData['order_id'])) {
            $orderId = $checkoutData['order_id'];
            
            $orderItemIds = OrderItem::where('order_id', $orderId)->pluck('id');
            
            if ($orderItemIds->isNotEmpty()) {
                OrderItemOption::whereIn('order_item_id', $orderItemIds)->delete();
            }
            
            OrderItem::where('order_id', $orderId)->delete();
            Order::where('id', $orderId)->delete();
        }
        
        session()->forget('checkout_data');
        return view('frontend.payment-cancelled');
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

            $oldStatus = $order->status;

            $statusMap = [
                'new' => 'pending',
                'accepted' => 'confirmed',
                'in_preparation' => 'preparing',
                'ready' => 'ready',
                'in_delivery' => 'out_for_delivery',
                'delivered' => 'delivered',
                'completed' => 'delivered',
                'cancelled' => 'cancelled',
                'rejected' => 'cancelled'
            ];

            $newLocalStatus = $statusMap[$orderStatus] ?? $orderStatus;

            $order->update(['status' => $newLocalStatus]);

            // Handle gift card when order is cancelled/rejected
            if (($orderStatus === 'cancelled' || $orderStatus === 'rejected') && $order->gift_card_id) {
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

            // Handle points when order is delivered
            if ($newLocalStatus === 'delivered' && $order->user_id) {

                if ($order->coupon_id) {
                    $coupon = Coupon::find($order->coupon_id);
                    if ($coupon) {
                        // Increment global usage
                        $coupon->increment('used_count');
                        
                        // Handle birthday voucher
                        if ($coupon->is_birthday_voucher) {
                            $coupon->users()->updateExistingPivot($order->user_id, [
                                'used_count' => 1
                            ]);
                        } else {
                            // Handle regular coupon - increment per-user usage
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

                // Deduct the points that were used for this order (if any)
                if ($order->points_used > 0) {
                    UserPoint::create([
                        'user_id' => $order->user_id,
                        'order_id' => $order->id,
                        'point' => -($order->points_used * 100)
                    ]);
                }

                // Add new points earned from total (after all discounts)
                $pointsEarned = floor($order->total);
                UserPoint::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'point' => $pointsEarned
                ]);
            }

            if ($orderStatus === 'accepted') {
            }

            if ($orderStatus === 'cancelled' || $orderStatus === 'rejected') {
                $cancellationReason = $newState['cancellation_reason'] ?? 'Not specified';
            }

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            
            return response()->json(['success' => false], 200);
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
        $staticPages = [
            ['loc' => url('/'), 'lastmod' => now()->toDateString(), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => url('/menu'), 'lastmod' => now()->toDateString(), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/our-story'), 'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => url('/find-us'), 'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => url('/contact'), 'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => url('/privacy-policy'), 'lastmod' => now()->toDateString(), 'changefreq' => 'yearly', 'priority' => '0.5'],
            ['loc' => url('/terms-and-conditions'), 'lastmod' => now()->toDateString(), 'changefreq' => 'yearly', 'priority' => '0.5']
        ];

        $urls = $staticPages;

        $content = view('frontend.sitemap', compact('urls'))->render();
        return Response::make($content, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }

}
