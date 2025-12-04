@extends('frontend.master')

@section('content')

@include('frontend.partials.banner', [
    'title' => 'Frequently Asked Questions',
    'image' => $page->banner_image ?? asset('banner.jpg')
])

<section id="faq" class="faq section">
  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row justify-content-center">
      <div class="col-lg-10">

        <div class="faq-list">
          @foreach($faqs as $index => $faq)
            <div class="faq-item" data-aos="fade-up" data-aos-delay="{{ 200 + ($index * 100) }}">
              <h3>
                <span class="num">{{ $index + 1 }}</span>
                <span class="question">{{ $faq->question }}</span>
                <i class="bi bi-plus-lg faq-toggle"></i>
              </h3>
              <div class="faq-content">
                {!! $faq->answer !!}
              </div>
            </div>
          @endforeach
        </div>

        <div class="faq-cta text-center mt-5" data-aos="fade-up" data-aos-delay="300">
          <p>Still have questions? We're here to help!</p>
          <a href="{{ route('contact') }}" class="btn btn-primary">Contact Support</a>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection