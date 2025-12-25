<!DOCTYPE html>
<html lang="en">
@php
    $company = App\Models\CompanyDetails::firstOrCreate();
@endphp

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    {!! Twitter::generate() !!}
    <meta property="og:type" content="website">

    @if($company->google_site_verification)
    <meta name="google-site-verification" content="{{ $company->google_site_verification }}">
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

    @include('frontend.cart')

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