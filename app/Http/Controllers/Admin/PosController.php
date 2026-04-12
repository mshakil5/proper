<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\GiftCard;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductOptionItem;
use App\Models\User;
use App\Models\UserPoint;
use App\Mail\OrderConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class PosController extends Controller
{
    public function pos()
    {
        $categories = Category::with([
            'products' => function ($q) {
                $q->where('status', 1)
                ->where('stock_status', 'in_stock')
                ->with(['options'])
                ->orderBy('sl', 'asc')
                ->orderBy('price', 'asc');
            }
        ])
            ->where('status', 1)
            ->orderBy('sl', 'asc')
            ->get();

        $clients = User::where('user_type', 2)->orderBy('name')->get();

        return view('admin.pos.create', compact('categories', 'clients'));
    }

    public function posGetProduct(Request $request)
    {
        $product = Product::with(['options.items.product'])->find($request->id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $html = view('admin.pos.product-modal', compact('product'))->render();

        return response()->json([
            'html'          => $html,
            'has_options'   => $product->options()->exists(),
            'has_attribute' => (bool) $product->has_attribute,
            'price'         => $product->price,
            'title'         => $product->title,
            'image'         => asset($product->image),
            'sku_ref'       => $product->sku_ref,
        ]);
    }

    public function posQuickCustomer(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name'  => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email',
            'phone'      => 'required|string',
            'password'   => 'nullable|string|min:6',
        ]);

        $user = User::create([
            'name'       => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => strtolower($request->email),
            'phone'      => preg_replace('/\s+/', '', $request->phone),
            'password'   => Hash::make($request->password ?? 'Password123!'),
            'user_type'  => '2',
            'status'     => 1,
            'image'      => '/placeholder.webp',
            'last_login' => now(),
        ]);

        UserPoint::create([
            'user_id'     => $user->id,
            'order_id'    => null,
            'source'      => 'registration_bonus',
            'point'       => 500,
            'description' => 'Registration bonus points',
        ]);

        return response()->json([
            'id'               => $user->id,
            'name'             => $user->name,
            'phone'            => $user->phone,
            'email'            => $user->email,
            'available_points' => 500,
        ]);
    }

    public function posGetCustomerInfo(Request $request)
    {
        $request->validate(['customer_id' => 'required|integer|exists:users,id']);
        $user = User::find($request->customer_id);

        return response()->json([
            'id'               => $user->id,
            'name'             => $user->name,
            'email'            => $user->email,
            'phone'            => $user->phone,
            'available_points' => $user->available_points ?? 0,
        ]);
    }

    public function posValidatePromo(Request $request)
    {
        $request->validate([
            'code'        => 'required|string',
            'subtotal'    => 'required|numeric|min:0',
            'customer_id' => 'nullable|integer|exists:users,id',
        ]);

        $code     = strtoupper(trim($request->code));
        $subtotal = $request->subtotal;
        $userId   = $request->customer_id;

        $giftCard = GiftCard::where('code', $code)->first();
        if ($giftCard) {
            if (!$giftCard->is_active)                                    return response()->json(['message' => 'Gift card is inactive'], 400);
            if ($giftCard->status === 'used')                             return response()->json(['message' => 'Gift card already used'], 400);
            if ($giftCard->status === 'expired')                          return response()->json(['message' => 'Gift card has expired'], 400);
            if ($giftCard->expires_at && $giftCard->expires_at < now())  return response()->json(['message' => 'Gift card has expired'], 400);

            $discount = min($giftCard->balance, $subtotal);
            return response()->json([
                'valid'           => true,
                'type'            => 'gift_card',
                'discount_amount' => (float) $discount,
                'code_data'       => ['id' => $giftCard->id, 'code' => $giftCard->code, 'balance' => (float) $giftCard->balance],
            ]);
        }

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon)               return response()->json(['message' => 'Invalid coupon or gift card code'], 404);
        if (!$coupon->is_active)    return response()->json(['message' => 'Coupon is inactive'], 400);
        if ($coupon->start_date && $coupon->start_date > now()) return response()->json(['message' => 'Coupon not yet active'], 400);
        if ($coupon->end_date   && $coupon->end_date < now())   return response()->json(['message' => 'Coupon has expired'], 400);
        if ($coupon->max_uses   && $coupon->used_count >= $coupon->max_uses) return response()->json(['message' => 'Coupon reached maximum usage'], 400);

        if ($coupon->max_uses_per_user && $userId) {
            $usage = CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $userId)->first();
            if ($usage && $usage->usage_count >= $coupon->max_uses_per_user) {
                return response()->json(['message' => 'Customer reached maximum usage for this coupon'], 400);
            }
        }

        if ($coupon->is_birthday_voucher && $userId) {
            $bv = $coupon->users()->where('user_id', $userId)->first();
            if ($bv && $bv->pivot->used_count > 0) {
                return response()->json(['message' => 'Customer already used this birthday voucher'], 400);
            }
        }

        if ($coupon->min_order_amount > 0 && $subtotal < $coupon->min_order_amount) {
            return response()->json(['message' => 'Minimum order £' . number_format($coupon->min_order_amount, 2) . ' required'], 400);
        }

        $discount = $coupon->discount_type === 'percent'
            ? ($subtotal * $coupon->discount_value) / 100
            : $coupon->discount_value;

        return response()->json([
            'valid'           => true,
            'type'            => 'coupon',
            'discount_amount' => (float) $discount,
            'code_data'       => [
                'id'             => $coupon->id,
                'code'           => $coupon->code,
                'discount_type'  => $coupon->discount_type,
                'discount_value' => $coupon->discount_value,
            ],
        ]);
    }

    public function posPlaceOrder(Request $request)
    {
        $request->validate([
            'customer.firstName' => 'required|string|max:100',
            'customer.lastName'  => 'required|string|max:100',
            'customer.email'     => 'required|email|max:255',
            'customer.phone'     => 'required',
            'customer_id'        => 'nullable|integer|exists:users,id',
            'delivery.type'      => 'required|in:delivery,collection',
            'delivery.time'      => 'required',
            'delivery.postcode'  => 'nullable|string|max:20',
            'address'            => 'nullable|string|max:255',
            'address2'           => 'nullable|string|max:255',
            'city'               => 'nullable|string|max:100',
            'cart'               => 'required|array|min:1',
            'cart.*.productId'   => 'required|integer|exists:products,id',
            'cart.*.quantity'    => 'required|integer|min:1|max:999',
            'cart.*.type'        => 'nullable|string',
            'cart.*.options'     => 'nullable|array',
            'paymentMethod'      => 'required|in:cash,stripe,paypal',
            'pointsToUse'        => 'nullable|integer|min:0',
            'promoCode'          => 'nullable|string|max:100',
            'notes'              => 'nullable|string|max:1000',
        ]);

        $customer      = $request->input('customer');
        $delivery      = $request->input('delivery');
        $cart          = $request->input('cart');
        $paymentMethod = $request->input('paymentMethod');
        $pointsToUse   = (int) ($request->input('pointsToUse') ?? 0);
        $promoCode     = $request->input('promoCode');
        $notes         = $request->input('notes');
        $customerId    = $request->input('customer_id');

        $address   = $request->input('address', '');
        $address2  = $request->input('address2', '');
        $city      = $request->input('city', '');

        // Force phone to string (This fixes the HubRise error)
        if (isset($customer['phone'])) {
            $customer['phone'] = (string) $customer['phone'];
        }

        $subtotal  = 0;
        $cartItems = [];

        foreach ($cart as $item) {
            $product = Product::find($item['productId']);
            if (!$product) {
                return response()->json(['success' => false, 'message' => 'Product not found'], 400);
            }

            $itemPrice      = $product->price;
            $optionPrice    = 0;
            $attributePrice = 0;

            if (($item['attribute'] ?? false) && $product->has_attribute) {
                $attributePrice = (float) $product->attribute_price;
            }

            if (($item['type'] ?? '') === 'custom' && !empty($item['options'])) {
                foreach ($item['options'] as $optionValues) {
                    foreach ($optionValues as $opt) {
                        $optionItem = ProductOptionItem::where('hubrise_option_ref', $opt['hubriseOptionRef'] ?? '')->first();
                        if ($optionItem) $optionPrice += $optionItem->override_price;
                    }
                }
            }

            $unitPrice      = $itemPrice + $optionPrice + $attributePrice;
            $totalItemPrice = $unitPrice * (int) $item['quantity'];
            $subtotal      += $totalItemPrice;

            $cartItems[] = [
                'productId'      => $item['productId'],
                'product'        => $product,
                'quantity'       => $item['quantity'],
                'basePrice'      => $itemPrice,
                'optionPrice'    => $optionPrice,
                'attributePrice' => $attributePrice,
                'unitPrice'      => $unitPrice,
                'totalPrice'     => $totalItemPrice,
                'options'        => $item['options'] ?? [],
            ];
        }

        $subtotal = round($subtotal, 2);

        $deliveryCharge = 0;
        if ($delivery['type'] === 'delivery') {
            $postcode = $delivery['postcode'] ?? '';

            if (empty($postcode) || empty($address) || empty($city)) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Postcode, street address and city are required for delivery'
                ], 400);
            }

            $deliveryData = $this->getDeliveryCharge($postcode);
            if (!$deliveryData['available']) {
                return response()->json(['success' => false, 'message' => 'Delivery not available for this postcode'], 400);
            }
            $deliveryCharge = $deliveryData['charge'];
        }
        $deliveryCharge = round($deliveryCharge, 2);

        $promoDiscount = 0;
        $promoType     = null;
        $promoId       = null;

        if ($promoCode) {
            $promoValidation = $this->validatePromoCodeBackend($promoCode, $subtotal, $customerId);
            if (!$promoValidation['valid']) {
                return response()->json(['success' => false, 'message' => $promoValidation['message']], 400);
            }
            $promoDiscount = $promoValidation['discount_amount'];
            $promoType     = $promoValidation['type'];
            $promoId       = $promoValidation['code_data']['id'];
        }
        $promoDiscount = round($promoDiscount, 2);

        $pointsDiscount = 0;
        if ($pointsToUse > 0 && $customerId) {
            $posUser    = User::find($customerId);
            $userPoints = $posUser ? ($posUser->available_points ?? 0) : 0;
            if ($pointsToUse > $userPoints) {
                return response()->json(['success' => false, 'message' => 'Insufficient points'], 400);
            }
            $pointsDiscount = round($pointsToUse / 100, 2);
            $remaining      = $subtotal + $deliveryCharge - $promoDiscount;
            if ($pointsDiscount > $remaining) $pointsDiscount = $remaining;
        }
        $pointsDiscount = round($pointsDiscount, 2);

        $total = round($subtotal + $deliveryCharge - $promoDiscount - $pointsDiscount, 2);
        if ($total < 0) $total = 0;

        $calculationData = [
            'subtotal'       => $subtotal,
            'deliveryCharge' => $deliveryCharge,
            'promoDiscount'  => $promoDiscount,
            'promoType'      => $promoType,
            'promoId'        => $promoId,
            'pointsToUse'    => $pointsToUse,
            'pointsDiscount' => $pointsDiscount,
            'total'          => $total,
            'cartItems'      => $cartItems,
            'customer'       => $customer,
            'customer_id'    => $customerId,
            'delivery'       => $delivery,
            'address'        => $address,
            'address2'       => $address2,
            'city'           => $city,
            'paymentMethod'  => $paymentMethod,
            'notes'          => $notes,
            'order_type'     => 'pos',
        ];

        if ($paymentMethod === 'cash')   return $this->processCashOrder($calculationData);
        if ($paymentMethod === 'stripe') return $this->processPosStripePayment($calculationData);
        if ($paymentMethod === 'paypal') return $this->processPosPayPalPayment($calculationData);
    }

    private function processCashOrder($calculationData)
    {
        try {
            $order = $this->createPosOrder($calculationData);
            $this->sendToHubRise($order, $calculationData);
            return response()->json(['success' => true, 'orderNumber' => $order->order_number, 'orderId' => $order->id]);
        } catch (\Exception $e) {
            \Log::error('POS Cash Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error placing order: ' . $e->getMessage()], 400);
        }
    }

    private function processPosStripePayment($calculationData)
    {
        try {
            $stripeCredential = \App\Models\Credential::where('gateway', 'Stripe')->first();
            if (!$stripeCredential || !$stripeCredential->client_secret) {
                return response()->json(['success' => false, 'message' => 'Stripe credentials not configured'], 400);
            }

            $payment = Payment::create([
                'user_id'        => $calculationData['customer_id'] ?? null,
                'payment_type'   => 'order',
                'reference_id'   => 0,
                'amount'         => $calculationData['total'],
                'currency'       => 'GBP',
                'payment_method' => 'stripe',
                'status'         => 'pending',
                'metadata'       => json_encode($calculationData),
            ]);

            session(['pos_payment_id' => $payment->id, 'pos_checkout_data' => $calculationData]);

            \Stripe\Stripe::setApiKey($stripeCredential->client_secret);

            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items'           => [[
                    'price_data' => [
                        'currency'     => 'GBP',
                        'product_data' => ['name' => 'POS Order'],
                        'unit_amount'  => intval($calculationData['total'] * 100),
                    ],
                    'quantity' => 1,
                ]],
                'mode'           => 'payment',
                'success_url'    => route('admin.pos.payment.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'     => route('admin.pos.payment.cancel'),
                'customer_email' => $calculationData['customer']['email'],
                'metadata'       => ['payment_id' => $payment->id],
            ]);

            $payment->update(['transaction_id' => $session->id]);
            return response()->json(['success' => true, 'redirectUrl' => $session->url]);
        } catch (\Exception $e) {
            \Log::error('POS Stripe Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create Stripe session'], 400);
        }
    }

    private function processPosPayPalPayment($calculationData)
    {
        try {
            $payment = Payment::create([
                'user_id'        => $calculationData['customer_id'] ?? null,
                'payment_type'   => 'order',
                'reference_id'   => 0,
                'amount'         => $calculationData['total'],
                'currency'       => 'GBP',
                'payment_method' => 'paypal',
                'status'         => 'pending',
                'metadata'       => json_encode($calculationData),
            ]);

            session(['pos_payment_id' => $payment->id, 'pos_checkout_data' => $calculationData]);

            $this->setPayPalConfig();
            $provider = new \Srmklive\PayPal\Services\PayPal;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $response = $provider->createOrder([
                'intent'              => 'CAPTURE',
                'application_context' => [
                    'return_url' => route('admin.pos.payment.success'),
                    'cancel_url' => route('admin.pos.payment.cancel'),
                ],
                'purchase_units' => [[
                    'amount' => ['currency_code' => 'GBP', 'value' => number_format($calculationData['total'], 2, '.', '')],
                ]],
            ]);

            if (!isset($response['id'])) throw new \Exception($response['message'] ?? 'Failed to create PayPal order');

            $payment->update(['transaction_id' => $response['id']]);

            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') return response()->json(['success' => true, 'redirectUrl' => $link['href']]);
            }

            throw new \Exception('PayPal approval link not found');
        } catch (\Exception $e) {
            \Log::error('POS PayPal Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'PayPal error: ' . $e->getMessage()], 400);
        }
    }

    public function posPaymentSuccess(Request $request)
    {
        $paymentId       = session('pos_payment_id');
        $calculationData = session('pos_checkout_data');

        if (!$paymentId || !$calculationData) {
            return redirect()->route('admin.pos')->with('error', 'Invalid payment session');
        }

        $payment = Payment::find($paymentId);
        if (!$payment) return redirect()->route('admin.pos')->with('error', 'Payment not found');

        try {
            if ($payment->payment_method === 'stripe') {
                $sessionId = $request->input('session_id');
                if (!$sessionId) throw new \Exception('Session ID missing');
                $cred = \App\Models\Credential::where('gateway', 'Stripe')->first();
                \Stripe\Stripe::setApiKey($cred->client_secret);
                $s = \Stripe\Checkout\Session::retrieve($sessionId);
                if ($s->payment_status !== 'paid') throw new \Exception('Payment not confirmed');
            } elseif ($payment->payment_method === 'paypal') {
                $token = $request->input('token');
                if (!$token) throw new \Exception('PayPal token missing');
                $this->setPayPalConfig();
                $provider = new \Srmklive\PayPal\Services\PayPal;
                $provider->setApiCredentials(config('paypal'));
                $provider->getAccessToken();
                $res = $provider->capturePaymentOrder($token);
                if (!isset($res['status']) || $res['status'] !== 'COMPLETED') {
                    throw new \Exception($res['message'] ?? 'PayPal capture failed');
                }
            }

            $payment->update(['status' => 'completed']);
            $order = $this->createPosOrder($calculationData);
            $payment->update(['reference_id' => $order->id]);
            $this->sendToHubRise($order, $calculationData);
            session()->forget(['pos_payment_id', 'pos_checkout_data']);

            return redirect()->route('admin.pos')->with('success', 'Order #' . $order->order_number . ' placed successfully!');
        } catch (\Exception $e) {
            \Log::error('POS Payment Success Error: ' . $e->getMessage());
            return redirect()->route('admin.pos')->with('error', 'Payment error: ' . $e->getMessage());
        }
    }

    public function posPaymentCancel()
    {
        $paymentId = session('pos_payment_id');
        if ($paymentId) Payment::where('id', $paymentId)->update(['status' => 'failed']);
        session()->forget(['pos_payment_id', 'pos_checkout_data']);
        return redirect()->route('admin.pos')->with('error', 'Payment was cancelled');
    }

    private function createPosOrder($calculationData)
    {
        return DB::transaction(function () use ($calculationData) {
            $orderNumber = 'ORD-' . time() . '-' . rand(1000, 9999);
            $customerId  = $calculationData['customer_id'] ?? null;
            $posUser     = $customerId ? User::find($customerId) : null;

            $order = Order::create([
                'user_id'            => $customerId,
                'order_number'       => $orderNumber,
                'customer_type'      => $posUser ? 'authenticated' : 'guest',
                'order_type'         => 'pos',
                'first_name'         => $calculationData['customer']['firstName'],
                'last_name'          => $calculationData['customer']['lastName'],
                'email'              => $calculationData['customer']['email'],
                'phone'              => $calculationData['customer']['phone'],
                'address_1'          => $calculationData['address'] ?? '',
                'address_2'          => $calculationData['address2'] ?? '',
                'city'               => $calculationData['city'] ?? '',
                'postcode'           => $calculationData['delivery']['postcode'] ?? null,
                'delivery_type'      => $calculationData['delivery']['type'],
                'time'               => $calculationData['delivery']['time'],
                'subtotal'           => $calculationData['subtotal'],
                'delivery_charge'    => $calculationData['deliveryCharge'],
                'coupon_discount'    => $calculationData['promoType'] === 'coupon' ? $calculationData['promoDiscount'] : 0,
                'coupon_id'          => $calculationData['promoType'] === 'coupon' ? $calculationData['promoId'] : null,
                'gift_card_discount' => $calculationData['promoType'] === 'gift_card' ? $calculationData['promoDiscount'] : 0,
                'gift_card_id'       => $calculationData['promoType'] === 'gift_card' ? $calculationData['promoId'] : null,
                'points_used'        => $calculationData['pointsToUse'] / 100,
                'total'              => $calculationData['total'],
                'payment_method'     => $calculationData['paymentMethod'],
                'payment_status'     => 'pending',
                'status'             => 'pending',
                'notes'              => $calculationData['notes'],
                'hubrise_order_id'   => null,
            ]);

            if ($posUser) {
                $posUser->increment('total_orders');
                $posUser->update(['last_order_date' => now()]);
            }

            foreach ($calculationData['cartItems'] as $item) {
                $orderItem = OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item['productId'],
                    'product_name' => $item['product']->title,
                    'sku_ref'      => $item['product']->sku_ref,
                    'quantity'     => $item['quantity'],
                    'price'        => $item['unitPrice'],
                    'total'        => $item['totalPrice'],
                ]);

                if (!empty($item['options'])) {
                    foreach ($item['options'] as $optionName => $optionValues) {
                        foreach ($optionValues as $opt) {
                            OrderItemOption::create([
                                'order_item_id'    => $orderItem->id,
                                'option_list_name' => $optionName,
                                'option_name'      => $opt['title'],
                                'option_ref'       => $opt['hubriseOptionRef'] ?? null,
                                'price'            => $opt['price'] ?? 0,
                            ]);
                        }
                    }
                }
            }

            $this->allocatePosOrderResources($order, $calculationData);

            return $order;
        });
    }

    private function allocatePosOrderResources($order, $calculationData)
    {
        $customerId = $calculationData['customer_id'] ?? null;

        if ($calculationData['promoType'] === 'gift_card' && $calculationData['promoId']) {
            $giftCard = GiftCard::find($calculationData['promoId']);
            if ($giftCard) {
                $newBalance = $giftCard->balance - $calculationData['promoDiscount'];
                $giftCard->update([
                    'balance'     => $newBalance,
                    'status'      => $newBalance <= 0 ? 'used' : 'new',
                    'order_id'    => $order->id,
                    'redeemed_by' => $customerId,
                    'redeemed_at' => now(),
                ]);
            }
        }

        if ($order->coupon_id) {
            $coupon = Coupon::find($order->coupon_id);
            if ($coupon) {
                $coupon->increment('used_count');
                if ($customerId) {
                    if ($coupon->is_birthday_voucher) {
                        $coupon->users()->updateExistingPivot($customerId, ['used_count' => 1]);
                    } else {
                        CouponUsage::updateOrCreate(
                            ['coupon_id' => $coupon->id, 'user_id' => $customerId],
                            ['usage_count' => DB::raw('usage_count + 1')]
                        );
                    }
                }
            }
        }

        if ($customerId && $calculationData['pointsToUse'] > 0) {
            UserPoint::create(['user_id' => $customerId, 'order_id' => $order->id, 'point' => -$calculationData['pointsToUse']]);
        }

        if ($customerId) {
            UserPoint::create(['user_id' => $customerId, 'order_id' => $order->id, 'point' => floor($order->total)]);
        }
    }

    private function sendToHubRise($order, $calculationData)
    {
        $accessToken = config('services.hubrise.access_token');
        $locationId  = config('services.hubrise.location_id');

        if (!$accessToken || !$locationId) {
            \Log::warning('HubRise credentials not configured — skipping POS sync');
            return;
        }

        $items = [];
        foreach ($calculationData['cartItems'] as $item) {
            $itemData = [
                'product_name' => $item['product']->title,
                'sku_ref'      => $item['product']->sku_ref,
                'price'        => number_format($item['unitPrice'], 2, '.', '') . ' GBP',
                'quantity'     => $item['quantity'],
                'options'      => [],
            ];
            if (!empty($item['options'])) {
                foreach ($item['options'] as $optionName => $optionValues) {
                    foreach ($optionValues as $opt) {
                        $itemData['options'][] = [
                            'option_list_name' => 'Option',
                            'name'             => $opt['title'],
                            'ref'              => (string) ($opt['hubriseOptionRef'] ?? ''),
                            'price'            => '0.00 GBP',
                        ];
                    }
                }
            }
            $items[] = $itemData;
        }

        $paymentRef  = 'CASH';
        $paymentName = 'Cash on Delivery';
        if ($calculationData['paymentMethod'] === 'stripe') { $paymentRef = 'STRIPE'; $paymentName = 'Stripe'; }
        if ($calculationData['paymentMethod'] === 'paypal') { $paymentRef = 'PAYPAL'; $paymentName = 'PayPal'; }

        $payload = [
            'status'           => 'new',
            'channel'          => 'POS',
            'service_type'     => $calculationData['delivery']['type'],
            'service_type_ref' => $calculationData['delivery']['type'] === 'delivery' ? '9' : '10',
            'items'            => $items,
            'payments'         => [['name' => $paymentName, 'ref' => $paymentRef, 'amount' => number_format($calculationData['total'], 2, '.', '') . ' GBP']],
            'customer'         => [
                'first_name' => $calculationData['customer']['firstName'],
                'last_name'  => $calculationData['customer']['lastName'],
                'email'      => $calculationData['customer']['email'],
                'phone'      => (string) ($calculationData['customer']['phone'] ?? ''),
                'address_1'  => $calculationData['address'] ?? '',
                'address_2'  => $calculationData['address2'] ?? '',
                'city'       => $calculationData['city'] ?? '',
                'postal_code'=> $calculationData['delivery']['postcode'] ?? null,
            ],
        ];

        if ($calculationData['notes'])          $payload['customer_notes'] = $calculationData['notes'];
        if ($calculationData['deliveryCharge'] > 0) {
            $payload['charges'] = [['name' => 'Delivery', 'price' => number_format($calculationData['deliveryCharge'], 2, '.', '') . ' GBP']];
        }
        if ($calculationData['promoDiscount'] > 0) {
            $payload['discounts'] = [['name' => $calculationData['promoType'] === 'gift_card' ? 'Gift Card' : 'Coupon', 'price_off' => number_format($calculationData['promoDiscount'], 2, '.', '') . ' GBP']];
        }
        if ($calculationData['pointsDiscount'] > 0) {
            if (!isset($payload['discounts'])) $payload['discounts'] = [];
            $payload['discounts'][] = ['name' => 'Points Redeemed', 'price_off' => number_format($calculationData['pointsDiscount'], 2, '.', '') . ' GBP'];
        }

        $response = Http::withHeaders(['X-Access-Token' => $accessToken, 'Content-Type' => 'application/json'])
            ->post("https://api.hubrise.com/v1/locations/{$locationId}/orders", $payload);

        if (!$response->successful()) {
            \Log::error('POS HubRise Error: ' . $response->body());
            return;
        }

        $order->update(['hubrise_order_id' => $response->json()['id'] ?? null, 'status' => 'pending']);

        try {
            if ($order->email && $order->email !== 'pos@internal.local') {
                Mail::to($order->email)->send(new OrderConfirmationMail($order));
            }
        } catch (\Exception $e) {
            \Log::warning('POS email error: ' . $e->getMessage());
        }
    }

    private function validatePromoCodeBackend($code, $subtotal, $userId = null)
    {
        $code     = strtoupper(trim($code));
        $giftCard = GiftCard::where('code', $code)->first();

        if ($giftCard) {
            if (!$giftCard->is_active)                                    return ['valid' => false, 'message' => 'Gift card is inactive'];
            if ($giftCard->status === 'used')                             return ['valid' => false, 'message' => 'Gift card already used'];
            if ($giftCard->expires_at && $giftCard->expires_at < now())  return ['valid' => false, 'message' => 'Gift card has expired'];
            return ['valid' => true, 'type' => 'gift_card', 'discount_amount' => min($giftCard->balance, $subtotal), 'code_data' => ['id' => $giftCard->id, 'code' => $giftCard->code]];
        }

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon)               return ['valid' => false, 'message' => 'Invalid coupon or gift card code'];
        if (!$coupon->is_active)    return ['valid' => false, 'message' => 'Coupon is inactive'];
        if ($coupon->end_date && $coupon->end_date < now())  return ['valid' => false, 'message' => 'Coupon has expired'];
        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) return ['valid' => false, 'message' => 'Coupon reached maximum usage'];
        if ($coupon->min_order_amount > 0 && $subtotal < $coupon->min_order_amount) {
            return ['valid' => false, 'message' => 'Minimum order £' . number_format($coupon->min_order_amount, 2) . ' required'];
        }

        $discount = $coupon->discount_type === 'percent'
            ? ($subtotal * $coupon->discount_value) / 100
            : $coupon->discount_value;

        return ['valid' => true, 'type' => 'coupon', 'discount_amount' => $discount, 'code_data' => ['id' => $coupon->id, 'code' => $coupon->code, 'discount_type' => $coupon->discount_type, 'discount_value' => $coupon->discount_value]];
    }

    private function getDeliveryCharge($postcode)
    {
        $centerLatitude  = 53.223912;
        $centerLongitude = -0.532985;
        $deliveryRadius  = 7.5;

        try {
            $response = Http::get('https://api.postcodes.io/postcodes/' . $postcode);
            if (!$response->successful()) return ['available' => false];

            $data      = $response->json();
            $latitude  = $data['result']['latitude'];
            $longitude = $data['result']['longitude'];

            $lat1Rad  = deg2rad($centerLatitude);
            $lon1Rad  = deg2rad($centerLongitude);
            $lat2Rad  = deg2rad($latitude);
            $lon2Rad  = deg2rad($longitude);
            $dlat     = $lat2Rad - $lat1Rad;
            $dlon     = $lon2Rad - $lon1Rad;
            $a        = sin($dlat / 2) ** 2 + cos($lat1Rad) * cos($lat2Rad) * sin($dlon / 2) ** 2;
            $distance = 3959 * 2 * atan2(sqrt($a), sqrt(1 - $a));

            if ($distance <= $deliveryRadius) {
                return ['available' => true, 'charge' => $distance > 4 ? 3.00 : 2.00, 'distance' => $distance];
            }
            return ['available' => false];
        } catch (\Exception $e) {
            return ['available' => false];
        }
    }

    private function setPayPalConfig()
    {
        $credential = \App\Models\Credential::where('gateway', 'PayPal')->first();
        if ($credential) {
            config([
                'paypal.mode'                  => $credential->mode ?? 'sandbox',
                'paypal.sandbox.client_id'     => $credential->client_id ?? '',
                'paypal.sandbox.client_secret' => $credential->client_secret ?? '',
                'paypal.live.client_id'        => $credential->client_id ?? '',
                'paypal.live.client_secret'    => $credential->client_secret ?? '',
                'paypal.payment_action'        => 'Sale',
                'paypal.currency'              => 'GBP',
                'paypal.notify_url'            => '',
                'paypal.locale'                => 'en_GB',
                'paypal.validate_ssl'          => true,
            ]);
        }
    }
}