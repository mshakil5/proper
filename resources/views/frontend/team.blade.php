@extends('frontend.master')

@section('content')

<div class="page-title light-background">
  <div class="container">
    <nav class="breadcrumbs">
      <ol>
        <li><a href="{{ route('home') }}">Home</a></li>
        <li class="current">Team</li>
      </ol>
    </nav>
  </div>
</div>

<section id="team" class="team section">
  <div class="site-section slider-team-wrap">
    <div class="container">
      <div class="slider-nav d-flex justify-content-end mb-3">
        <a href="#" class="js-prev js-custom-prev"><i class="bi bi-arrow-left-short"></i></a>
        <a href="#" class="js-next js-custom-next"><i class="bi bi-arrow-right-short"></i></a>
      </div>
      <div class="swiper init-swiper" data-aos="fade-up" data-aos-delay="100">
        <script type="application/json" class="swiper-config">
          {
            "loop": true,
            "speed": 600,
            "autoplay": {
              "delay": 5000
            },
            "slidesPerView": "1",
            "pagination": {
              "el": ".swiper-pagination",
              "type": "bullets",
              "clickable": true
            },
            "navigation": {
              "nextEl": ".js-custom-next",
              "prevEl": ".js-custom-prev"
            },
            "breakpoints": {
              "640": {
                "slidesPerView": 2,
                "spaceBetween": 30
              },
              "768": {
                "slidesPerView": 3,
                "spaceBetween": 30
              },
              "1200": {
                "slidesPerView": 3,
                "spaceBetween": 30
              }
            }
          }
        </script>
        <div class="swiper-wrapper">
            @foreach($teamMembers as $member)
            <div class="swiper-slide">
                <div class="team">
                    <div class="pic">
                        <img src="{{ asset('images/team-members/' . $member->image) }}" 
                            alt="{{ $member->name }}" class="img-fluid">
                    </div>
                    <h3>
                      <span>{{ $member->title ?? '' }}</span> {{ $member->name }}
                    </h3>
                    <p>{!! $member->description !!}</p>
                </div>
            </div>
            @endforeach
        </div>
      </div>
    </div>
  </div>
</section>

@endsection