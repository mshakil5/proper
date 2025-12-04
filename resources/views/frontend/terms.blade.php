@extends('frontend.master')

@section('content')

  @include('frontend.partials.banner', [
      'title' => 'Terms & Conditions',
      'image' => $page->banner_image ?? asset('banner.jpg')
  ])

  <section id="terms" class="terms section">
    <div class="container section-title" data-aos="fade-up">
      <h2>Terms & Conditions</h2>
    </div>

    <div class="container" data-aos="fade-up">
      <div class="row">
        <div class="col-12">
          <div class="terms-content">
            {!! $companyDetails->terms_and_conditions !!}
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection