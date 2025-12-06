<nav class="navbar navbar-expand-lg bg-white py-3 shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
            @if(isset($company->company_logo) && $company->company_logo != '')
                <img 
                    id="company_logo_preview"
                    src="{{ asset('images/company/' . $company->company_logo) }}"
                    alt="Company Logo"
                    class="me-2"
                    style="width:180px; height:40px; object-fit:contain;"
                >
            @endif

            <span class="fw-bold fs-5 d-none">{{ $company->company_name ?? '' }}</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('menu') }}">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('our-story') }}">Our Story</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('find-us') }}">Find Us</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('contact') }}">Contact</a></li>
            </ul>
            <a href="https://www.propertakeaways.com/order" class="btn btn-gradient ms-3 fw-semibold">
                <i class="fa-solid fa-bag-shopping me-1"></i> Order Now
            </a>
        </div>
    </div>
</nav>

<div class="marquee-bar">
    <div class="marquee-wrapper overflow-hidden position-relative">
        <div class="marquee-content">
            <span>Fast Delivery •</span>
            <span>Authentic Taste •</span>
            <span>Fresh Food •</span>
            <span>Fast Delivery •</span>
            <span>Authentic Taste •</span>
            <span>Fresh Food •</span>
        </div>
    </div>
</div>