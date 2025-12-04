@extends('frontend.master')

@section('content')
    @include('frontend.partials.banner', [
        'title' => 'About Us',
        'image' => $page->banner_image ?? asset('banner.jpg'),
    ])

    <section class="services section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <div class="services-content" data-aos="fade-left" data-aos-duration="900">
                        <span class="subtitle">{{ $about1->short_title ?? '' }}</span>
                        <h2>{{ $about1->long_title ?? '' }}</h2>
                        <p data-aos="fade-right" data-aos-duration="800">{!! $about1->long_description ?? '' !!}</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="services-image" data-aos="fade-left" data-aos-delay="200">
                        <img src="{{ asset('images/meta_image/' . $about1->meta_image) }}" class="img-fluid"
                            alt="{{ $about1->short_title ?? '' }}">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="services section light-background">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <div class="services-image" data-aos="fade-right" data-aos-duration="900">
                        <img src="{{ asset('images/meta_image/' . $about2->meta_image) }}" class="img-fluid rounded"
                            alt="{{ $about2->short_title ?? '' }}">
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="services-content" data-aos="fade-left" data-aos-delay="200">
                        <span class="subtitle">{{ $about2->short_title ?? '' }}</span>
                        <h2>{{ $about2->long_title ?? '' }}</h2>
                        <p data-aos="fade-right" data-aos-duration="800">{!! $about2->long_description ?? '' !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="services section">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <div class="services-content" data-aos="fade-left" data-aos-duration="900">
                        <span class="subtitle">{{ $about3->short_title ?? '' }}</span>
                        <h2>{{ $about3->long_title ?? '' }}</h2>
                        <p data-aos="fade-right" data-aos-duration="800">{!! $about3->long_description ?? '' !!}</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="services-image" data-aos="fade-left" data-aos-delay="200">
                        <img src="{{ asset('images/meta_image/' . $about3->meta_image) }}" class="img-fluid"
                            alt="{{ $about3->short_title ?? '' }}">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="services section light-background">
        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row align-items-center mb-5">
                <div class="col-lg-6">
                    <div class="services-content" data-aos="fade-left" data-aos-duration="900">
                        <p data-aos="fade-right" data-aos-duration="800">{!! $about4->long_description ?? '' !!}</p>
                    </div>
                </div>
                <div class="col-lg-2">
                </div>
                <div class="col-lg-4">
                    <div class="contact-card p-4 rounded shadow-sm bg-light d-inline-block book-now-section mt-3">
                        <p class="mb-3 fw-bold text-dark">
                            Don't hesitate, Book Now for better help and services.
                        </p>
                        <a href="{{ route('book-now') }}" class="book-now-btn">
                            <span>Book Now</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        .book-now-section {
            margin-top: 30px;
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid color-mix(in srgb, var(--default-color), transparent 90%);
        }

        .book-now-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, var(--accent-color) 0%, #d4b57d 30%, #b8944e 100%);
            color: #fff;
            padding: 14px 32px;
            border-radius: 50px;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(197, 165, 114, 0.4);
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(197, 165, 114, 0.3);
        }

        .book-now-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            transition: left 0.5s ease;
        }

        .book-now-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(197, 165, 114, 0.6);
            color: #f5f5f5;
        }

        .book-now-btn:hover::before {
            left: 100%;
        }

        .book-now-btn:active {
            transform: translateY(-1px);
        }

        .book-now-btn i {
            transition: transform 0.3s ease;
        }

        .book-now-btn:hover i {
            transform: translateX(4px);
        }
    </style>
@endsection