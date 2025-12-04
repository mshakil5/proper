@extends('frontend.master')

@section('content')
  @include('frontend.partials.banner', [
      'title' => 'Privacy Policy',
      'image' => $page->banner_image ?? asset('banner.jpg')
  ])

  <section id="privacy" class="privacy section">
    <div class="container section-title" data-aos="fade-up">
      <h2>Privacy Policy</h2>
    </div>

    <div class="container" data-aos="fade-up">
      <div class="row">
        <div class="col-12">
          <div class="privacy-content">
            {!! $companyPrivacy->privacy_policy !!}
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection