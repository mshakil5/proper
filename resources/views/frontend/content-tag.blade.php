@extends('frontend.master')

@section('content')
<div class="page-title light-background">
  <div class="container">
    <nav class="breadcrumbs">
      <ol>
        <li><a href="{{ route('home') }}">Home</a></li>
        <li class="current">{{ $tag->name }}</li>
      </ol>
    </nav>
  </div>
</div>

<section id="{{ $type }}-{{ $tag->slug }}-posts" class="blog-posts section">
    <div class="container section-title" data-aos="fade-up">
        <h2>{{ ucfirst($type) }} tagged "{{ $tag->name }}"</h2>
    </div>

    <div class="container">
        <div class="row gy-4">
            @forelse($contents as $content)
                <div class="col-md-6 col-lg-4">
                    <div class="post-entry" data-aos="fade-up" data-aos-delay="100">
                        <a href="{{ route('content.show', ['type' => $type, 'slug' => $content->slug]) }}" class="thumb d-block">
                            <img src="{{ asset('images/content/'.$content->feature_image) }}" 
                                 alt="{{ $content->short_title ?? $content->title }}" 
                                 class="img-fluid rounded">
                        </a>

                        <div class="post-content">
                            <div class="meta">
                                <a href="{{ route('content.category', ['type' => $type, 'slug' => $content->category->slug]) }}" class="cat">
                                    {{ $content->category->name ?? '' }}
                                </a> •
                                <span class="date">{{ date('F d, Y', strtotime($content->publishing_date ?? $content->created_at)) }}</span>
                            </div>

                            <h3>
                                <a href="{{ route('content.show', ['type' => $type, 'slug' => $content->slug]) }}">
                                    {{ $content->short_title ?? $content->title }}
                                </a>
                            </h3>

                            <p>{{ $content->long_title ?? $content->description }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p>No {{ $type }} found with the tag "{{ $tag->name }}".</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
@endsection