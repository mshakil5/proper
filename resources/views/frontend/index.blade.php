@extends('frontend.master')

@section('content')
    @foreach ($sections as $section)
        @if ($section->name == 'hero')
            <section class="banner-section">
                <div class="container">
                    <div class="row align-items-start">

                        <div class="col-md-6 mt-0">
                            <div class="rating-badge mb-3">
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                <i class="fa-solid fa-star"></i>
                                Rated 4.9 by 2,000+ customers
                            </div>

                            <h1 class="banner-title display-1 fw-bold">
                                {{ $hero->short_title ?? '' }} <br />
                                <span class="highlight">{{ $hero->long_title ?? '' }}</span> <br /> {{ $hero->short_description ?? '' }}
                            </h1>

                            <p class="text-secondary mt-3 pe-5 mb-4 fs-5 fw-bold">
                                {!! $hero->long_description ?? '' !!}
                            </p>

                            <div class="mt-4 d-flex flex-wrap gap-2 align-items-center mb-5">
                                <a href="#product" class="btn btn-gradient px-4 py-3 mb-2 fw-bold">
                                    Explore Menu <i class="fa-solid fa-arrow-right ms-2"></i>
                                </a>
                                <a href="#our-story" class="btn btn-outline-dark px-4 py-3 mb-2 fw-bold btn-rounded">
                                    Our Story
                                </a>
                            </div>

                            <!-- Delivery Stats -->
                            <div class="delivery-stats mt-5 d-flex gap-4">
                                <div class="delivery-stat d-flex align-items-center">
                                    <div class="stat-icon me-3">
                                        <i class="fa-solid fa-clock text-warning fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="stat-value fw-bold fs-4">25 min</div>
                                        <div class="stat-label text-muted small">Avg. Delivery</div>
                                    </div>
                                </div>

                                <div class="delivery-stat d-flex align-items-center">
                                    <div class="stat-icon me-3">
                                        <i class="fa-solid fa-truck text-warning fs-4"></i>
                                    </div>
                                    <div>
                                        <div class="stat-value fw-bold fs-4">Free</div>
                                        <div class="stat-label text-muted small">Delivery £25+</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 position-relative mt-4 mt-md-0">
                            <div id="heroSlider" class="carousel slide position-relative" data-bs-ride="carousel">

                                <!-- Indicators -->
                                <div class="carousel-indicators">
                                    @foreach($sliders as $key => $slider)
                                        <button type="button" data-bs-target="#heroSlider" data-bs-slide-to="{{ $key }}" 
                                                class="{{ $key == 0 ? 'active' : '' }}"></button>
                                    @endforeach
                                </div>

                                <!-- Slides -->
                                <div class="carousel-inner rounded-4 shadow-lg">
                                    @foreach($sliders as $key => $slider)
                                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                                            <img src="{{ asset('images/slider/' . $slider->image) }}" class="d-block w-100" alt="{{ $slider->title }}">
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Prev/Next -->
                                <button class="carousel-control-prev" type="button" data-bs-target="#heroSlider" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#heroSlider" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>

                                <!-- Floating Badges -->
                                <div class="dish-badge">50+<br><small class="text-muted">Dishes</small></div>
                                <div class="open-badge">24/7<br><small class="fw-normal">Open</small></div>

                            </div>
                        </div>

                    </div>
                </div>

            </section>

            <div class="wave-container">
                <div class="u-curve"></div>
            </div>
        @endif

        @if ($section->name == 'menu')
            @include('frontend.partials.menu')
        @endif

        @if ($section->name == 'our-story')
            @include('frontend.partials.our-story')
        @endif

        @if ($section->name == 'find-us')
            @include('frontend.partials.find-us')
        @endif
    @endforeach
@endsection