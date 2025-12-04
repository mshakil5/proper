@extends('frontend.master')

@section('content')
<section id="{{ $slug }}-posts" class="blog-posts section">
    <div class="container section-title" data-aos="fade-up">
        <h2>{{ ucfirst($slug) }}</h2>
    </div>
    <div class="container">
        <div class="row gy-4">
            @foreach($contents as $content)
            <div class="col-md-6 col-lg-4">
                <div class="post-entry" data-aos="fade-up" data-aos-delay="100">
                    <a href="{{ route('content.show', ['type' => $slug, 'slug' => $content->slug]) }}" class="thumb d-block">
                        <img src="{{ asset('images/content/'.$content->feature_image) }}" 
                             alt="{{ $content->short_title ?? $content->title }}" 
                             class="img-fluid rounded">
                    </a>

                    <div class="post-content">
                        <div class="meta">
                            <a href="{{ route('content.category', ['type' => $slug, 'slug' => $content->category->slug ?? '']) }}" class="cat">{{ $content->category->name ?? '' }}</a> •
                            <span class="date">{{ date('F d, Y', strtotime($content->publishing_date ?? $content->created_at)) }}</span>
                        </div>

                        <h3>
                            <a href="{{ route('content.show', ['type' => $slug, 'slug' => $content->slug]) }}">{{ $content->short_title ?? $content->title }}</a>
                        </h3>

                        <p>{{ $content->long_title ?? $content->description }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

@endsection

@section('script')

@endsection