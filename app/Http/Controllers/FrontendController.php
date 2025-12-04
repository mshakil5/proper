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

class FrontendController extends Controller
{
    public function index()
    {
      $company = CompanyDetails::first();
      $hero = Master::firstOrCreate(['name' => 'hero']);
      $findUs = Master::firstOrCreate(['name' => 'find-us']);
      $sliders = Cache::remember('active_sliders', now()->addDay(), function () {
          return Slider::where('status', 1)->latest()->get();
      });

      $sections = Section::where('status', 1)
          ->orderBy('sl', 'asc')
          ->get();

      $this->seo(
          $company?->meta_title ?? '',
          $company?->meta_description ?? '',
          $company?->meta_keywords ?? '',
          $company?->meta_image ? asset('images/company/meta/' . $company->meta_image) : null
      );

      return view('frontend.index', compact('hero', 'findUs', 'sliders','sections','company'));
    }

    public function aboutUs()
    {
        $companyDetails = CompanyDetails::select('about_us')->first();
        $banner =  Banner::firstOrCreate(['page' => 'About']);
        $about1 = Master::firstOrCreate(['name' => 'About-Section-1']);
        $about2 = Master::firstOrCreate(['name' => 'About-Section-2']);
        $about3 = Master::firstOrCreate(['name' => 'About-Section-3']);
        $about4 = Master::firstOrCreate(['name' => 'About-Section-4']);
        $this->seo(
            $about1->meta_title,
            $about1->meta_description,
            $about1->meta_keywords,
            $about1->meta_image ? asset('images/meta_image/' . $about1->meta_image) : null
        );
        return view('frontend.about', compact('companyDetails', 'banner', 'about1', 'about2', 'about3', 'about4'));
    }

    public function menu()
    {
        $menu = Master::firstOrCreate(['name' => 'Menu']);
        $this->seo(
            $menu->meta_title,
            $menu->meta_description,
            $menu->meta_keywords,
            $menu->meta_image ? asset('images/meta_image/' . $menu->meta_image) : null
        );
       return view('frontend.menu');
    }

    public function ourStory()
    {
        $menu = Master::firstOrCreate(['name' => 'our-story']);
        $this->seo(
            $menu->meta_title,
            $menu->meta_description,
            $menu->meta_keywords,
            $menu->meta_image ? asset('images/meta_image/' . $menu->meta_image) : null
        );
        return view('frontend.our-story');
    }

    public function findUs()
    {
        $menu = Master::firstOrCreate(['name' => 'find-us']);
        $this->seo(
            $menu->meta_title,
            $menu->meta_description,
            $menu->meta_keywords,
            $menu->meta_image ? asset('images/meta_image/' . $menu->meta_image) : null
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
            $menu->meta_image ? asset('images/meta_image/' . $menu->meta_image) : null
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
        $contact->company = $request->input('company');
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
        $banner = null;
        $companyPrivacy = Cache::rememberForever('company_privacy', function () {
            return CompanyDetails::select('privacy_policy')->first();
        });

        return view('frontend.privacy', compact('companyPrivacy', 'banner'));
    }

    public function termsAndConditions()
    {
        $banner = null;
        $companyDetails = Cache::rememberForever('company_terms', function () {
            return CompanyDetails::select('terms_and_conditions')->first();
        });
        return view('frontend.terms', compact('companyDetails', 'banner'));
    }

    public function frequentlyAskedQuestions()
    {   
        $banner = null;
        $faqs = FaqQuestion::orderBy('id', 'asc')->get();
        return view('frontend.faq', compact('faqs', 'banner'));
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
    }

    public function sitemap()
    {
        $urls = [];
        
        $staticPages = [
            ['loc' => url('/'), 'lastmod' => now()->toDateString(), 'changefreq' => 'daily', 'priority' => '1.0'],
            ['loc' => url('/about-us'), 'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => url('/menu'), 'lastmod' => now()->toDateString(), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/gallery'), 'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => url('/services'), 'lastmod' => now()->toDateString(), 'changefreq' => 'weekly', 'priority' => '0.9'],
            ['loc' => url('/book-now'), 'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => url('/contact'), 'lastmod' => now()->toDateString(), 'changefreq' => 'monthly', 'priority' => '0.7'],
        ];

        $urls = array_merge($urls, $staticPages);

        $services = Product::where('status', 1)->get();
        foreach ($services as $service) {
            $urls[] = [
                'loc' => url('/service/' . $service->slug),
                'lastmod' => $service->updated_at->toDateString(),
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ];
        }

        $content = view('frontend.sitemap', compact('urls'))->render();
        return Response::make($content, 200)->header('Content-Type', 'application/xml');
    }

}
