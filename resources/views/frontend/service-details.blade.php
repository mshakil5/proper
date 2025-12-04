@extends('frontend.master')

@section('content')
    @include('frontend.partials.banner', [
        'title' => $service->title ?? 'Service',
        'image' => $page->banner_image ?? asset('banner.jpg'),
    ])

  <div class="container">
    <div class="row">

      <div class="col-lg-8">

        <section id="blog-details" class="blog-details section">
          <div class="container">

            <article class="article">

              <div class="post-img">
                <img src="{{ asset('images/products/' . $service->image) }}" alt="{{ $service->title ?? '' }}" class="img-fluid">
              </div>

              <h2 class="title">{{ $service->title }}</h2>

              <div class="content">
                <p>
                  {!! $service->long_description !!}
                </p>

              </div>

            </article>

          </div>
        </section>

      </div>

      <div class="col-lg-4 sidebar">
          <div class="widgets-container">
              <div class="recent-posts-widget widget-item">
                  <h3 class="widget-title">Other Services</h3>

                  @foreach(App\Models\Product::where('status', 1)
                      ->where('id', '!=', $service->id)
                      ->select('title', 'slug', 'short_description', 'image')
                      ->latest()
                      ->take(3)
                      ->get() as $other)

                  <div class="post-item d-flex mb-3">
                      <img src="{{ asset('images/products/' . $other->image) }}" 
                          alt="{{ $other->title }}" class="flex-shrink-0 me-3" style="width: 60px; height: 60px; object-fit: cover;">
                      <div>
                          <h4>
                              <a href="{{ route('service.show', $other->slug) }}">
                                  {{ $other->title }}
                              </a>
                          </h4>
                          <p>{{ \Illuminate\Support\Str::limit($other->short_description, 100) }}</p>
                      </div>
                  </div>

                  @endforeach

                  <div class="post-item d-flex justify-content-center mt-4">
                      <a href="{{ route('book-now') }}">
                          <img src="{{ asset('features.jpg') }}" alt="Book Now" class="img-fluid rounded" style="width: 100%;">
                      </a>
                  </div>

              </div>
          </div>
      </div>

    </div>
  </div>

@endsection