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

    public function placeOrder(Request $request)
    {
        try {
            Log::info('Place order request received', $request->all());
            // return;
            $hubRiseOrder = $request->input('hubRiseOrder');
            $localOrder = $request->input('localOrder');

            $accessToken = env('HUBRISE_ACCESS_TOKEN');
            $locationId = env('HUBRISE_LOCATION_ID');

            Log::info('HubRise Config', [
                'token' => $accessToken ? 'Set' : 'Not Set',
                'location' => $locationId
            ]);

            if (!$accessToken || !$locationId) {
                Log::error('HubRise credentials missing');
                return response()->json([
                    'success' => false,
                    'message' => 'HubRise credentials not configured'
                ], 500);
            }

            // Build correct HubRise order format based on documentation
            $fixedOrder = [
                'status' => $hubRiseOrder['status'] ?? 'new',
                'channel' => $hubRiseOrder['channel'] ?? 'Website',
                'service_type' => $hubRiseOrder['service_type'] ?? 'delivery',
                'items' => [],
                'payments' => $hubRiseOrder['payments'] ?? [],
                'customer' => [
                    'first_name' => $hubRiseOrder['customer']['first_name'] ?? '',
                    'last_name' => $hubRiseOrder['customer']['last_name'] ?? '',
                    'email' => $hubRiseOrder['customer']['email'] ?? null,
                    'phone' => $hubRiseOrder['customer']['phone_number'] ?? null,
                    'address_1' => $hubRiseOrder['customer']['address'] ?? null
                ]
            ];

            // Add customer notes if present
            if (!empty($hubRiseOrder['customer_notes'])) {
                $fixedOrder['customer_notes'] = $hubRiseOrder['customer_notes'];
            }

            // Build items array with correct format
            foreach ($hubRiseOrder['items'] as $item) {
                $fixedItem = [
                    'product_name' => $item['product_name'] ?? '',
                    'quantity' => (float) ($item['quantity'] ?? 1),
                    'price' => $item['price'] ?? '0.00 BDT'
                ];

                // Add options if present
                if (!empty($item['options']) && is_array($item['options'])) {
                    $fixedItem['options'] = [];
                    foreach ($item['options'] as $option) {
                        $fixedItem['options'][] = [
                            'option_list_name' => 'Options',
                            'name' => $option['name'] ?? '',
                            'price' => '0.00 BDT'
                        ];
                    }
                }

                $fixedOrder['items'][] = $fixedItem;
            }

            // Add charges if present (delivery charge)
            if (!empty($hubRiseOrder['charges']) && is_array($hubRiseOrder['charges'])) {
                $fixedOrder['charges'] = [];
                foreach ($hubRiseOrder['charges'] as $charge) {
                    $fixedOrder['charges'][] = [
                        'name' => $charge['name'] ?? 'Charge',
                        'price' => $charge['amount'] ?? '0.00 BDT'
                    ];
                }
            }

            Log::info('Fixed HubRise Order', $fixedOrder);

            Log::info('Sending to HubRise API', [
                'url' => "https://api.hubrise.com/v1/locations/{$locationId}/orders"
            ]);

            $response = Http::withHeaders([
                'X-Access-Token' => $accessToken,
                'Content-Type' => 'application/json'
            ])->post(
                "https://api.hubrise.com/v1/locations/{$locationId}/orders",
                $fixedOrder
            );

            Log::info('HubRise Response Status', ['status' => $response->status()]);
            Log::info('HubRise Response', ['body' => $response->json()]);

            if ($response->successful()) {
                $hubRiseData = $response->json();
                $hubRiseOrderId = $hubRiseData['id'] ?? null;

                Log::info('Order created successfully', ['hubrise_id' => $hubRiseOrderId]);

                // Optional: Save to database
                // DB::table('orders')->insert([
                //     'hubrise_id' => $hubRiseOrderId,
                //     'customer_first_name' => $localOrder['customer']['firstName'],
                //     'customer_last_name' => $localOrder['customer']['lastName'],
                //     'customer_email' => $localOrder['customer']['email'],
                //     'customer_phone' => $localOrder['customer']['phone'],
                //     'customer_address' => $localOrder['customer']['address'],
                //     'customer_city' => $localOrder['customer']['city'],
                //     'customer_postcode' => $localOrder['customer']['postalCode'],
                //     'items' => json_encode($localOrder['cart']),
                //     'delivery_type' => $localOrder['delivery']['type'],
                //     'delivery_postcode' => $localOrder['delivery']['postcode'],
                //     'delivery_time' => $localOrder['delivery']['time'],
                //     'delivery_charge' => $localOrder['deliveryCharge'],
                //     'subtotal' => $localOrder['subtotal'],
                //     'total' => $localOrder['total'],
                //     'status' => 'pending',
                //     'created_at' => now()
                // ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Order placed successfully',
                    'orderId' => $hubRiseOrderId,
                    'hubRiseData' => $hubRiseData
                ]);
            } else {
                $errorData = $response->json();
                Log::error('HubRise API Error', ['error' => $errorData]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to place order with HubRise',
                    'error' => $errorData
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Exception in placeOrder', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error placing order: ' . $e->getMessage()
            ], 500);
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
