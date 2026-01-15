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
use Illuminate\Support\Facades\Log;
use App\Models\DeliveryZone;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use Illuminate\Support\Str;
use App\Models\UserPoint;

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

        $centerLatitude = 51.996611;
        $centerLongitude = -0.802070;
        $deliveryRadius = 7.5;
        $deliveryCharge = 2.00;

        $latitude = (float) $request->latitude;
        $longitude = (float) $request->longitude;
        $postcode = strtoupper(trim($request->postcode));

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

        if ($distance <= $deliveryRadius) {
            return response()->json([
                'available' => true,
                'delivery_charge' => (float) $deliveryCharge,
                'distance' => round($distance, 2),
                'postcode' => $postcode,
                'message' => 'Delivery available'
            ]);
        }

        return response()->json([
            'available' => false,
            'distance' => round($distance, 2),
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
        
        $centerLatitude = 51.996611;
        $centerLongitude = -0.802070;
        $deliveryRadius = 7.5;
        $deliveryCharge = 2.00;

        $latitude = (float) $request->latitude;
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
                'message' => 'Outside delivery area',
                'distance' => round($distance, 2)
            ], 422);
        }

        $addresses = $this->getAddressesFromNominatim($latitude, $longitude, $postcode);

        if (empty($addresses)) {
            return response()->json([
                'message' => 'No addresses found for this area',
            ], 422);
        }

        return response()->json([
            'available' => true,
            'delivery_charge' => (float) $deliveryCharge,
            'distance' => round($distance, 2),
            'postcode' => $postcode,
            'addresses' => $addresses,
            'message' => 'Delivery available'
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

    public function checkout()
    {
        return view('frontend.checkout');
    }

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string|uppercase',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $coupon = Coupon::where('code', $request->code)->first();

        if (!$coupon) {
            return response()->json([
                'message' => 'Invalid coupon code'
            ], 404);
        }

        if (!$coupon->is_active) {
            return response()->json([
                'message' => 'This coupon is inactive'
            ], 400);
        }

        if ($coupon->end_date && $coupon->end_date < now()) {
            return response()->json([
                'message' => 'This coupon has expired'
            ], 400);
        }

        if ($coupon->max_uses && $coupon->used_count >= $coupon->max_uses) {
            return response()->json([
                'message' => 'This coupon has reached its maximum usage limit'
            ], 400);
        }

        if ($coupon->min_order_amount > 0 && $request->subtotal < $coupon->min_order_amount) {
            return response()->json([
                'message' => 'Minimum order amount of £' . number_format($coupon->min_order_amount, 2) . ' required'
            ], 400);
        }

        $discount_amount = 0;
        
        if ($coupon->discount_type === 'percent') {
            $discount_amount = ($request->subtotal * $coupon->discount_value) / 100;
        } else {
            $discount_amount = $coupon->discount_value;
        }

        return response()->json([
            'valid' => true,
            'discount_amount' => $discount_amount,
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'discount_type' => $coupon->discount_type,
                'discount_value' => $coupon->discount_value
            ]
        ], 200);
    }

    public function placeOrder(Request $request)
    {
        try {
            $hubRiseOrder = $request->input('hubRiseOrder');
            $localOrder = $request->input('localOrder');

            $accessToken = env('HUBRISE_ACCESS_TOKEN');
            $locationId = env('HUBRISE_LOCATION_ID');

            // if (!$accessToken || !$locationId) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'HubRise credentials not configured'
            //     ], 500);
            // }

            // Validate service type
            $serviceType = $hubRiseOrder['service_type'] ?? 'delivery';
            if (!in_array($serviceType, ['delivery', 'collection'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid service type'
                ], 400);
            }

            // For delivery only - validate address
            if ($serviceType === 'delivery') {
                $customer = $hubRiseOrder['customer'] ?? [];
                if (empty($customer['address_1']) || empty($customer['city']) || empty($customer['postal_code'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Address required for delivery orders'
                    ], 400);
                }
            }

            // Payment ref mapping
            $paymentRefMap = [
                'cash' => '22',
                'stripe' => '23',
                'paypal' => '24'
            ];

            // Create order in database
            $orderNumber = 'ORD-' . time() . '-' . rand(1000, 9999);

            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'customer_type' => $localOrder['customer']['type'],
                'first_name' => $localOrder['customer']['firstName'],
                'last_name' => $localOrder['customer']['lastName'],
                'email' => $localOrder['customer']['email'],
                'phone' => $localOrder['customer']['phone'],
                'address_1' => $localOrder['address'],
                'address_2' => $localOrder['address2'],
                'street' => $localOrder['address'],
                'city' => $localOrder['city'],
                'postcode' => $localOrder['postalCode'],
                'delivery_type' => $localOrder['delivery']['type'],
                'time' => $localOrder['delivery']['time'],
                'subtotal' => $localOrder['subtotal'],
                'delivery_charge' => $localOrder['deliveryCharge'],
                'coupon_discount' => $localOrder['coupon_discount'],
                'coupon_id' => $localOrder['coupon_id'],
                'points_used' => $localOrder['points_used'],
                'other_discount' => 0.00,
                'total' => $localOrder['total'],
                'payment_method' => $localOrder['paymentMethod'],
                'payment_status' => 'pending',
                'status' => 'pending',
                'notes' => $localOrder['orderNotes'] ?? null,
                'hubrise_order_id' => null,
                'payment_transaction_id' => null
            ]);

            // Save order items
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

                // Save item options
                if ($item['type'] === 'custom' && !empty($item['options'])) {
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

            // Route based on payment method
            if ($localOrder['paymentMethod'] === 'stripe') {
                return $this->initiateStripePayment($order, $hubRiseOrder, $localOrder, $accessToken, $locationId, $paymentRefMap);
            } elseif ($localOrder['paymentMethod'] === 'paypal') {
                return response()->json([
                    'success' => false,
                    'message' => 'PayPal not yet implemented'
                ], 400);
            } else {
                // Cash on Delivery
                $this->sendToHubRise($order, $hubRiseOrder, $localOrder, $accessToken, $locationId, $paymentRefMap);

                return response()->json([
                    'success' => true,
                    'confirmUrl' => route('order.confirmation', ['orderNumber' => $order->order_number]),
                    'orderId' => $order->id,
                    'orderNumber' => $order->order_number
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error placing order: ' . $e->getMessage()
            ], 500);
        }
    }

    private function initiateStripePayment($order, $hubRiseOrder, $localOrder, $accessToken, $locationId, $paymentRefMap)
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

            // Set Stripe API key
            Stripe::setApiKey(config('services.stripe.secret'));

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
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment session'
            ], 500);
        }
    }

    public function paymentSuccess(Request $request)
    {
        $orderId = $request->input('order_id');
        $checkoutData = session('checkout_data');

        if (!$checkoutData || $checkoutData['order_id'] != $orderId) {
            return view('frontend.payment-error', ['message' => 'Invalid payment session']);
        }

        $order = Order::find($orderId);
        if (!$order) {
            return view('frontend.payment-error', ['message' => 'Order not found']);
        }

        try {
            // Update order - payment confirmed
            $order->update([
                'payment_status' => 'paid',
                'status' => 'pending'
            ]);

            if (auth()->check()) {
                UserPoint::create([
                    'user_id' => auth()->id(),
                    'order_id' => $order->id,
                    'point' => -($order->points_used ?? 0)
                ]);
            }

            // Send to HubRise
            $this->sendToHubRise(
                $order,
                $checkoutData['hubRiseOrder'],
                $checkoutData['localOrder'],
                $checkoutData['accessToken'],
                $checkoutData['locationId'],
                $checkoutData['paymentRefMap']
            );

            // Clear session
            session()->forget('checkout_data');

            return view('frontend.payment-success', [
                'order' => $order,
                'orderNumber' => $order->order_number,
                'orderId' => $order->id
            ]);

        } catch (\Exception $e) {
            return view('frontend.payment-error', ['message' => 'Error processing order']);
        }
    }

    public function paymentCancel()
    {
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

    private function sendToHubRise($order, $hubRiseOrder, $localOrder, $accessToken, $locationId, $paymentRefMap)
    {
        // Prepare HubRise payload
        $hubRisePayload = [
            'status' => 'new',
            'channel' => 'Website',
            'service_type' => $hubRiseOrder['service_type'],
            'service_type_ref' => $hubRiseOrder['service_type'] === 'delivery' ? '9' : '10',
            'items' => [],
            'payments' => [
                [
                    'name' => $hubRiseOrder['payments'][0]['name'] ?? 'Online Payment',
                    'ref' => $paymentRefMap[$localOrder['paymentMethod']] ?? '22',
                    'amount' => $hubRiseOrder['payments'][0]['amount']
                ]
            ],
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
        // $response = Http::withHeaders([
        //     'X-Access-Token' => $accessToken,
        //     'Content-Type' => 'application/json'
        // ])->post(
        //     "https://api.hubrise.com/v1/locations/{$locationId}/orders",
        //     $hubRisePayload
        // );

        // if (!$response->successful()) {
        //     throw new \Exception('Failed to create order in HubRise');
        // }

        // $hubRiseData = $response->json();
        // $hubRiseOrderId = $hubRiseData['id'] ?? null;

        // Update order with HubRise ID
        $order->update([
            // 'hubrise_order_id' => $hubRiseOrderId,
            'hubrise_order_id' => str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT),
            'status' => 'pending'
        ]);
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
            Log::error('HubRise callback setup error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error setting up callback: ' . $e->getMessage()
            ], 500);
        }
    }

    public function hubRiseOrderCallback(Request $request)
    {
        try {
            // Get the callback data
            $data = $request->json()->all();

            Log::info('HubRise webhook received', [
                'resource_type' => $data['resource_type'] ?? null,
                'event_type' => $data['event_type'] ?? null,
                'order_id' => $data['order_id'] ?? null
            ]);

            $resourceType = $data['resource_type'] ?? null;
            $eventType = $data['event_type'] ?? null;
            $hubRiseOrderId = $data['order_id'] ?? null;
            $newState = $data['new_state'] ?? [];
            $orderStatus = $newState['status'] ?? null;

            // Only handle order.update events
            if ($resourceType !== 'order' || $eventType !== 'update') {
                Log::info('Ignoring non-order or non-update event', [
                    'resource_type' => $resourceType,
                    'event_type' => $eventType
                ]);
                return response()->json(['success' => true], 200);
            }

            if (!$hubRiseOrderId || !$orderStatus) {
                Log::warning('Missing order_id or status in webhook');
                return response()->json(['success' => true], 200);
            }

            // Find order by HubRise ID
            $order = Order::where('hubrise_order_id', $hubRiseOrderId)->first();

            if (!$order) {
                Log::warning('Order not found for HubRise ID: ' . $hubRiseOrderId);
                return response()->json(['success' => true], 200);
            }

            $oldStatus = $order->status;

            // Map HubRise status to local status
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

            // Update order status
            $order->update(['status' => $newLocalStatus]);

            Log::info('Order status updated from HubRise', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'hubrise_order_id' => $hubRiseOrderId,
                'hubrise_status' => $orderStatus,
                'old_status' => $oldStatus,
                'new_status' => $newLocalStatus
            ]);

            // Handle order accepted (confirmed)
            if ($orderStatus === 'confirmed') {
                Log::info('Order ACCEPTED by EPOS', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_email' => $order->email,
                    'customer_name' => $order->first_name . ' ' . $order->last_name
                ]);
                
                // TODO: Send acceptance email to customer
                // Mail::to($order->email)->send(new OrderAcceptedMail($order));
            }

            // Handle order cancelled/rejected
            if ($orderStatus === 'cancelled' || $orderStatus === 'rejected') {
                $cancellationReason = $newState['cancellation_reason'] ?? 'Not specified';
                
                Log::info('Order REJECTED/CANCELLED by EPOS', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer_email' => $order->email,
                    'customer_name' => $order->first_name . ' ' . $order->last_name,
                    'reason' => $cancellationReason
                ]);
                
                // TODO: Send rejection email to customer with reason
                // Mail::to($order->email)->send(new OrderRejectedMail($order, $cancellationReason));
            }

            // Return 200 to acknowledge receipt (HubRise will delete the event)
            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            Log::error('HubRise callback error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Still return 2xx to prevent HubRise retries
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
