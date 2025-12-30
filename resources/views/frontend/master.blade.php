<!DOCTYPE html>
<html lang="en">
@php
    $company = App\Models\CompanyDetails::firstOrCreate();
@endphp

<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="language" content="English">
    <meta name="author" content="{{ config('app.name') }}">
    <meta name="revisit-after" content="7 days">

    <link rel="alternate" hreflang="en-GB" href="{{ url()->current() }}">

    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}

    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "LocalBusiness",
            "name": "{{ $company->company_name ?? config('app.name') }}",
            "image": "{{ $company->logo ? asset('uploads/company/' . $company->logo) : '' }}",
            "description": "{{ $company->meta_description ?? '' }}",
            "url": "{{ url('/') }}",
            "telephone": "{{ $company->phone1 ?? '' }}",
            "email": "{{ $company->email ?? '' }}",
            "priceRange": "$$",
            "servesCuisine": ["Fast Food", "Takeaway", "Delivery"],
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "{{ $company->address1 ?? '' }}",
                "addressLocality": "{{ $company->city ?? 'Lincoln' }}",
                "postalCode": "{{ $company->postcode ?? '' }}",
                "addressCountry": "UK"
            },
            "openingHoursSpecification": [
                {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
                "opens": "16:30",
                "closes": "23:30"
                },
                {
                "@type": "OpeningHoursSpecification",
                "dayOfWeek": "Sunday",
                "opens": "16:30",
                "closes": "22:00"
                }
            ],
            "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "4.0",
                "reviewCount": "41"
            },
            "hasMenu": "{{ url('/menu') }}"
        }
    </script>

    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "FoodDeliveryService",
            "name": "{{ $company->company_name ?? config('app.name') }}",
            "url": "{{ url('/') }}",
            "telephone": "{{ $company->phone1 ?? '' }}",
            "areaServed": {
                "@type": "City",
                "name": "Lincoln"
            },
            "deliveryRange": {
                "@type": "Distance",
                "name": "7.5 miles"
            }
        }
    </script>

    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@type": "WebSite",
            "name": "{{ config('app.name') }}",
            "url": "{{ url('/') }}",
            "potentialAction": {
                "@type": "SearchAction",
                "target": {
                "@type": "EntryPoint",
                "urlTemplate": "{{ url('/menu') }}?q={search_term_string}"
                },
                "query-input": "required name=search_term_string"
            }
        }
    </script>

    @stack('ld-json')

    @if ($company->google_site_verification)
        <meta name="google-site-verification" content="{{ $company->google_site_verification }}">
    @endif

    @if ($company->google_analytics_id)
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $company->google_analytics_id }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '{{ $company->google_analytics_id }}');
        </script>
    @endif

    <link href="{{ asset('uploads/company/' . $company->fav_icon) }}" rel="icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="{{ asset('resources/backend/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('resources/frontend/css/custom.css') }}" rel="stylesheet">
</head>

<body>

    @include('frontend.header')

    @yield('content')

    @if (!request()->routeIs('checkout'))
        @include('frontend.cart')
    @endif

    @include('frontend.footer')

    @include('frontend.cookies')

    @include('frontend.product-modal')

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="{{ asset('resources/backend/libs/sweetalert2/sweetalert2.min.js') }}"></script>

    <script src="{{ asset('resources/frontend/js/custom.js') }}"></script>

    <script src="{{ asset('resources/frontend/js/cart.js') }}"></script>

    @yield('script')

</body>

</html>