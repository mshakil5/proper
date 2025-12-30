@extends('frontend.master')

@push('ld-json')
    <script type="application/ld+json">
        {
        "@@context": "https://schema.org",
        "@type": "Product",
        "name": "{{ $product->title }}",
        "description": "{{ $product->short_description ?? $product->long_description }}",
        "image": "{{ asset($product->image) }}",
        "brand": {
            "@type": "Brand",
            "name": "{{ config('app.name') }}"
        },
        "offers": {
            "@type": "Offer",
            "url": "{{ url()->current() }}",
            "priceCurrency": "GBP",
            "price": "{{ $product->price }}",
            "availability": "https://schema.org/InStock"
        },
        }
    </script>
@endpush

@section('content')
    <section class="product-details-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="product-details-image">
                        <img src="{{ asset($product->image) }}" alt="{{ $product->title }}" class="img-fluid rounded-4"
                            loading="lazy">
                        @if ($product->tag)
                            <span class="product-badge">{{ $product->tag->name }}</span>
                        @endif
                    </div>
                </div>

                <div class="col-md-6">
                    <h1 class="product-title mb-2">{{ $product->title }}</h1>
                    <span class="product-category d-block mb-3">{{ $product->category->name }}</span>

                    <div class="rating-section mb-4">
                        <div class="stars">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star-half-alt"></i>
                        </div>
                        <span class="rating-text">(4.5 out of 5)</span>
                    </div>

                    <div class="price-section mb-4">
                        <span class="price-value fs-3 fw-bold">£{{ number_format($product->price, 2) }}</span>
                    </div>

                    <div class="description-section mb-5">
                        <h3 class="fs-5 fw-bold mb-2">Description</h3>
                        <p class="product-description">
                            {!! $product->short_description ?? $product->long_description !!}
                        </p>
                    </div>

                    @if ($product->has_attribute)
                        <div class="product-section mb-4">
                            <div class="product-section-title mb-3">
                                <i class="fas fa-ruler"></i>
                                Size:
                            </div>
                            <div class="option-group">
                                <div class="option-item">
                                    <input type="radio" name="attribute_select" value="standalone" class="option-input"
                                        id="attr_standalone" disabled>
                                    <label for="attr_standalone" class="option-label">
                                        On its own
                                    </label>
                                </div>
                                <div class="option-item">
                                    <input type="radio" name="attribute_select" value="with_options" class="option-input"
                                        id="attr_with_options" disabled>
                                    <label for="attr_with_options" class="option-label">
                                        {{ $product->attribute_name }}
                                        @if ($product->attribute_price > 0)
                                            <span
                                                class="option-price">+£{{ number_format($product->attribute_price, 2) }}</span>
                                        @endif
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($product->options->count() > 0)
                        <div class="options-section">
                            <h3 class="fs-5 fw-bold mb-3">Options</h3>
                            @foreach ($product->options as $option)
                                <div class="product-section mb-4">
                                    <div class="product-section-title mb-2">
                                        <i class="fas fa-layer-group"></i>
                                        {{ $option->name }}
                                        @if ($option->is_required)
                                            <span class="required">*</span>
                                        @endif
                                    </div>
                                    <div class="option-group">
                                        @foreach ($option->items as $item)
                                            <div class="option-item">
                                                <input type="{{ $option->type === 'single' ? 'radio' : 'checkbox' }}"
                                                    name="option_{{ $option->id }}" value="{{ $item->product_id }}"
                                                    id="option_{{ $option->id }}_{{ $item->product_id }}" disabled
                                                    class="option-input">
                                                <label for="option_{{ $option->id }}_{{ $item->product_id }}"
                                                    class="option-label">
                                                    {{ $item->product->title }}
                                                </label>
                                                @if ($item->override_price > 0)
                                                    <span
                                                        class="option-price">+£{{ number_format($item->override_price, 2) }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                    @if ($option->type === 'multi')
                                        <small class="text-muted d-block mt-2">
                                            Max selections: <strong>{{ $option->max_select }}</strong>
                                        </small>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="cta-section">
                        <a href="{{ route('menu') }}" class="btn btn-gradient px-4 py-3 fw-bold">
                            Back to Menu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection