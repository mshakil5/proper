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
use App\Models\Product;
use App\Models\ProductOptionItem;
use App\Models\User;
use App\Models\UserPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class PosController extends Controller
{
    public function pos()
    {
        $categories = Category::with([
            'products' => function ($q) {
                $q->where('status', 1)
                    ->where('stock_status', 'in_stock')
                    ->with(['options.items.product'])
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
            if (!$giftCard->is_active)                                   return response()->json(['message' => 'Gift card is inactive'], 400);
            if ($giftCard->status === 'used')                            return response()->json(['message' => 'Gift card already used'], 400);
            if ($giftCard->status === 'expired')                         return response()->json(['message' => 'Gift card has expired'], 400);
            if ($giftCard->expires_at && $giftCard->expires_at < now()) return response()->json(['message' => 'Gift card has expired'], 400);

            $discount = min($giftCard->balance, $subtotal);
            return response()->json([
                'valid'           => true,
                'type'            => 'gift_card',
                'discount_amount' => (float) $discount,
                'code_data'       => ['id' => $giftCard->id, 'code' => $giftCard->code, 'balance' => (float) $giftCard->balance],
            ]);
        }

        $coupon = Coupon::where('code', $code)->first();
        if (!$coupon)            return response()->json(['message' => 'Invalid coupon or gift card code'], 404);
        if (!$coupon->is_active) return response()->json(['message' => 'Coupon is inactive'], 400);
        if ($coupon->start_date && $coupon->start_date > now()) return response()->json(['message' => 'Coupon not yet active'], 400);
        if ($coupon->end_date   && $coupon->end_date < now())   return response()->json(['message' => 'Coupon has expired'], 400);
        if ($coupon->max_uses   && $coupon->used_count >= $coupon->max_uses) return response()->json(['message' => 'Coupon reached maximum usage'], 400);

        if ($coupon->max_uses_per_user && $userId) {
            $usage = CouponUsage::where('coupon_id', $coupon->id)->where('user_id', $userId)->first();
            if ($usage && $usage->usage_count >= $coupon->max_uses_per_user) {
                return response()->json(['message' => 'Customer reached maximum usage for this coupon'], 400);
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
            'customer.email'     => 'nullable|email|max:255',
            'customer.phone'     => 'nullable|string|max:20',
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
            'paymentMethod'      => 'required|in:cash',
            'notes'              => 'nullable|string|max:1000',
        ]);

        $customer      = $request->input('customer');
        $delivery      = $request->input('delivery');
        $cart          = $request->input('cart');
        $customerId    = $request->input('customer_id');
        $notes         = $request->input('notes');

        $address  = $request->input('address', '');
        $address2 = $request->input('address2', '');
        $city     = $request->input('city', '');

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

        $total = round($subtotal + $deliveryCharge, 2);

        $calculationData = [
            'subtotal'       => $subtotal,
            'deliveryCharge' => $deliveryCharge,
            'promoDiscount'  => 0,
            'promoType'      => null,
            'promoId'        => null,
            'pointsToUse'    => 0,
            'pointsDiscount' => 0,
            'total'          => $total,
            'cartItems'      => $cartItems,
            'customer'       => $customer,
            'customer_id'    => $customerId,
            'delivery'       => $delivery,
            'address'        => $address,
            'address2'       => $address2,
            'city'           => $city,
            'paymentMethod'  => 'cash',
            'notes'          => $notes,
            'order_type'     => 'pos',
        ];

        try {
            $order = $this->createPosOrder($calculationData);
            return response()->json(['success' => true, 'orderNumber' => $order->order_number, 'orderId' => $order->id]);
        } catch (\Exception $e) {
            \Log::error('POS Order Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error placing order: ' . $e->getMessage()], 400);
        }
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
                'email'              => $calculationData['customer']['email'] ?? '',
                'phone'              => $calculationData['customer']['phone'] ?? '',
                'address_1'          => $calculationData['address'] ?? '',
                'address_2'          => $calculationData['address2'] ?? '',
                'city'               => $calculationData['city'] ?? '',
                'postcode'           => $calculationData['delivery']['postcode'] ?? null,
                'delivery_type'      => $calculationData['delivery']['type'],
                'time'               => $calculationData['delivery']['time'],
                'subtotal'           => $calculationData['subtotal'],
                'delivery_charge'    => $calculationData['deliveryCharge'],
                'coupon_discount'    => 0,
                'coupon_id'          => null,
                'gift_card_discount' => 0,
                'gift_card_id'       => null,
                'points_used'        => 0,
                'total'              => $calculationData['total'],
                'payment_method'     => 'cash',
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

            if ($customerId) {
                UserPoint::create(['user_id' => $customerId, 'order_id' => $order->id, 'point' => floor($order->total)]);
            }

            return $order;
        });
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

            $lat1Rad = deg2rad($centerLatitude);
            $lon1Rad = deg2rad($centerLongitude);
            $lat2Rad = deg2rad($latitude);
            $lon2Rad = deg2rad($longitude);
            $dlat    = $lat2Rad - $lat1Rad;
            $dlon    = $lon2Rad - $lon1Rad;
            $a       = sin($dlat / 2) ** 2 + cos($lat1Rad) * cos($lat2Rad) * sin($dlon / 2) ** 2;
            $distance = 3959 * 2 * atan2(sqrt($a), sqrt(1 - $a));

            if ($distance <= $deliveryRadius) {
                return ['available' => true, 'charge' => $distance > 4 ? 3.00 : 2.00, 'distance' => $distance];
            }
            return ['available' => false];
        } catch (\Exception $e) {
            return ['available' => false];
        }
    }

    public function posReceipt(Order $order)
    {
        $order->load(['items.options']);
        return view('admin.pos.receipt', compact('order'));
    }

    public function posReceiptCustomer(Order $order)
    {
        $order->load(['items.options']);
        return view('admin.pos.receipt_customer', compact('order'));
    }

    public function posReceiptKitchen(Order $order)
    {
        $order->load(['items.options']);
        return view('admin.pos.receipt_kitchen', compact('order'));
    }
}