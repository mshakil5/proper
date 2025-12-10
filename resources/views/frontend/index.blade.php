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
                                Rated 4.0 star by 41 Google reviews
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
                            <div class="delivery-stats mt-5">
                                <div class="row">
                                    <div class="col-6 mb-3 mb-md-0">
                                        <div class="delivery-stat d-flex align-items-center h-100">
                                            <div class="stat-icon me-3">
                                                <i class="fa-solid fa-clock text-warning fs-4"></i>
                                            </div>
                                            <div>
                                                <div class="stat-value fw-bold fs-4">£15</div>
                                                <div class="stat-label text-muted small">
                                                    Minimum Order<br>
                                                    Within 7.5 Miles
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6 mb-3 mb-md-0">
                                        <div class="delivery-stat d-flex align-items-center h-100">
                                            <div class="stat-icon me-3">
                                                <i class="fa-solid fa-clock text-warning fs-4"></i>
                                            </div>
                                            <div>
                                                <div class="stat-value fw-bold fs-4">30 min</div>
                                                <div class="stat-label text-muted small">Avg. Delivery</div>
                                            </div>
                                        </div>
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
                                <div class="dish-badge">140+<br><small class="text-muted">Dishes</small></div>
                                <div class="open-badge">365<br><small class="fw-normal">Open</small></div>

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

    @php
        $bottomAbout = App\Models\Master::firstOrCreate(['name' => 'bottom-about']);
    @endphp

    <section class="find-us container p-3 my-5">
        <div class="container">
            <div class="row">

            <div class="col-lg-5 mb-4 mb-lg-0 d-flex">
                <img src="{{ asset('images/meta_image/' . $bottomAbout->meta_image) }}" 
                    alt="Bottom About Image" 
                    style="width:100%; flex:1; object-fit:cover; border-radius:16px; 
                            transition: transform 0.5s ease; 
                            box-shadow: 0 6px 20px rgba(0,0,0,0.1);">
            </div>

            <div class="col-lg-7 d-flex flex-column" style="text-align: center;">
                <div>
                    <h2 class="story-title mb-4">{{ $bottomAbout->short_title ?? '' }} 
                    </h2>
                    <h3 class="accent fw-bold" style="font-size:25px; color:var(--orange)">{{ $bottomAbout->long_title ?? '' }}</h3>
                    <p class="story-desc mb-5">{!! $bottomAbout->long_description ?? '' !!}</p>
                </div>
            </div>

            </div>
        </div>
    </section>
@endsection