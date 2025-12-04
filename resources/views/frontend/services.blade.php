@extends('frontend.master')

@section('content')
    @include('frontend.partials.banner', [
        'title' => 'Services',
        'image' => $page->banner_image ?? asset('banner.jpg'),
    ])

    <section id="simplified-portfolio" class="simplified-portfolio section">

        <div class="container section-title">
            <h2>We Cater for</h2>
            <p>Weddings, Parties, Birthdays, Funerals, co-operate events, carnivals, Care Home Deliveries, Any other Functions/Occasions…We Also Have Our Takeaway Order Service</p>
        </div>

        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <div class="row gy-4" data-aos="fade-up" data-aos-delay="200">

                @foreach ($products as $product)
                <div class="col-lg-4 col-md-6 portfolio-item">
                    <div class="simplified-portfolio-card">
                        <div class="simplified-portfolio-img">
                            <img src="{{ asset('images/products/' . $product->image) }}" 
                                alt="{{ $product->title }}" class="img-fluid">
                            <div class="simplified-portfolio-overlay">
                                <a href="{{ route('service.show', $product->slug) }}" class="simplified-portfolio-link">
                                    <i class="bi bi-arrow-up-right"></i>
                                </a>
                            </div>
                        </div>
                        <div class="simplified-portfolio-info">
                            <h4>{{ $product->title ?? '' }}</h4>
                            <p>{{ $product->short_description ?? '' }}</p>
                        </div>
                    </div>
                </div>
                @endforeach

            </div>
        </div>

    </section>
@endsection