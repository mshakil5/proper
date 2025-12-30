@extends('frontend.master')

@push('ld-json')
<script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "ItemList",
        "itemListElement": [
            @foreach($products as $key => $product)
            {
            "@type": "Product",
            "position": {{ $loop->iteration }},
            "name": "{{ $product->title }}",
            "url": "{{ route('product.details', $product->slug) }}",
            "image": "{{ asset($product->image) }}",
            "description": "{{ $product->short_description ?? '' }}",
            "sku": "{{ $product->id }}",
            "offers": {
                "@type": "Offer",
                "priceCurrency": "GBP",
                "price": "{{ $product->price }}",
                "availability": "https://schema.org/InStock"
            }
            }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ]
    }
</script>
@endpush

@section('content')
    @include('frontend.partials.menu')
@endsection