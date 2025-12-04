@extends('frontend.master')

@section('content')
  <div class="page-title light-background">
    <div class="container">
      <nav class="breadcrumbs">
        <ol>
          <li><a href="{{ route('home') }}">Home</a></li>
          <li class="current">{{ $content->short_title ?? '' }}</li>
        </ol>
      </nav>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-lg-8">
        <section id="content-details" class="blog-details section">
          <div class="container">
            <article class="article">
              <div class="post-img">
                <img src="{{ asset('images/content/'.$content->feature_image) }}" 
                     alt="{{ $content->short_title ?? '' }}" 
                     class="img-fluid">
              </div>
              <h2 class="title">{{ $content->short_title ?? '' }}</h2>
              <div class="meta-top">
                <ul>
                  <li class="d-flex align-items-center"><i class="bi bi-person"></i>{{ $content->createdBy->name ?? '' }}</li>
                  <li class="d-flex align-items-center"><i class="bi bi-clock"></i>
                      <time datetime="{{ $content->publishing_date }}">
                        {{ date('d F Y', strtotime($content->publishing_date ?? $content->created_at)) }}
                      </time>
                  </li>
                </ul>
              </div>
              <div class="content">
                {!! $content->long_description ?? $content->description !!}
              </div>
              <div class="meta-bottom">
                <i class="bi bi-folder"></i>
                <ul class="cats">
                  <li><a href="{{ route('content.category', ['type' => $type, 'slug' => $content->category->slug]) }}" title="{{ $content->category->name ?? '' }}">{{ $content->category->name ?? '' }}</a></li>
                </ul>
                <i class="bi bi-tags"></i>
                <ul class="tags">
                  @foreach($content->tags as $tag)
                    <li>                
                      <a href="{{ route('content.tag', ['type' => $type, 'slug' => $tag->slug]) }}"title="{{ $tag->name }}">
                        {{ $tag->name }}
                      </a>
                    </li>
                  @endforeach
                </ul>
              </div>
            </article>
          </div>
        </section>
      </div>

      <div class="col-lg-4 sidebar">
        <div class="widgets-container">
          <div class="search-widget widget-item">
              <h3 class="widget-title">Search {{ ucfirst($type) }}</h3>
              <form id="ajax-search-form" class="php-email-form">
                  <input type="text" name="query" id="search-input" autocomplete="off" placeholder="Search..." required>
                  <button type="submit" title="Search"><i class="bi bi-search"></i></button>
              </form>
              <div id="search-results" class="recent-posts-widget widget-item" style="margin-top:10px; display:none;"></div>
          </div>

          @if (count($otherContents) > 0)
          <div class="recent-posts-widget widget-item">
            <h3 class="widget-title">Recent {{ ucfirst($type) }}</h3>
            @foreach($otherContents as $item)
              <div class="post-item">
                <h4>
                  <a href="{{ route('content.show', ['type' => $type, 'slug' => $item->slug]) }}">
                    {{ $item->short_title }}
                  </a>
                </h4>
                <time datetime="{{ $item->publishing_date }}">
                  {{ date('M j, Y', strtotime($item->publishing_date)) }}
                </time>
              </div>
            @endforeach
          </div>
          @endif

          <div class="tags-widget widget-item">
            <h3 class="widget-title">Tags</h3>
            <ul>
              @foreach($tags as $tag)
                <li>
                  <a href="{{ route('content.tag', ['type' => $type, 'slug' => $tag->slug]) }}" title="{{ $tag->name }}">
                    {{ $tag->name }}
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#ajax-search-form').submit(function(e) {
            e.preventDefault();
            var query = $('#search-input').val().trim();
            if(query == '') return;

            $.ajax({
                url: '/content-search',
                type: 'GET',
                data: { query: query, type: '{{ $type ?? "" }}' },
                success: function(res) {
                    if(res.trim() !== '') {
                        $('#search-results').html(res).show();
                    } else {
                        $('#search-results').hide();
                    }
                },
                error(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
@endsection