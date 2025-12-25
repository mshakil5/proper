@php
    $categories = App\Models\Category::with([
        'products' => function ($q) {
            $q->where('status', 1)->with('tag')->withCount('options');
        },
    ])
        ->where('status', 1)
        ->orderBy('sl', 'asc')
        ->get();
    $firstCategory = $categories->first();
    $firstCategoryName = $firstCategory ? strtolower($firstCategory->name) : 'all';

    $menu = App\Models\Master::firstOrCreate(['name' => 'Menu']);
@endphp
<section id="product" class="section bg-light">
    <div class="container-fluid">
        <section class="section-head">
            <h2 class="big-title">Popular <span class="accent">Picks</span></h2>
            <p class="subtxt">Discover our most-loved dishes, crafted with the finest ingredients and bursting with
                flavour</p>
        </section>
        <div class="category-pills" id="categoryPills">
            <div class="pill" data-filter="all">All</div>
            @foreach ($categories as $key => $category)
                <div class="pill {{ $key === 0 ? 'active' : '' }}" data-filter="{{ strtolower($category->name) }}">
                    {{ $category->name }}
                </div>
            @endforeach
        </div>
        <div class="cards-grid container" id="cardsGrid">
            @foreach ($categories as $category)
                @foreach ($category->products as $product)
                    <div class="food-card rounded-xl" data-cat="{{ strtolower($category->name) }}"
                        style="display: {{ strtolower($category->name) === $firstCategoryName ? 'block' : 'none' }};">
                        <div class="img-wrap">
                            <img src="{{ asset($product->image) }}" alt="{{ $product->title }}">
                            @if ($product->tag)
                                @if (strtolower($product->tag->name) == 'veg')
                                    <div class="badge-top veg">{{ $product->tag->name }}</div>
                                @else
                                    <div class="badge-top">{{ $product->tag->name }}</div>
                                @endif
                            @endif
                        </div>
                        <div class="card-body">
                            <h3 class="card-title">{{ $product->title }}</h3>
                            <div class="card-desc">{!! $product->short_description !!}</div>
                            <div class="card-foot">
                                <div class="tag">{{ $category->name }}</div>
                                <div style="display:flex;align-items:center;gap:18px;">
                                    <div class="price">£{{ number_format($product->price, 2) }}</div>
                                    @if($product->stock_status === 'in_stock')
                                    <a href="javascript:void(0)" 
                                        class="btn btn-gradient open-product"
                                        data-id="{{ $product->id }}"
                                        data-price="{{ $product->price }}"
                                        data-title="{{ $product->title }}"
                                        data-image="{{ $product->image }}"
                                        data-has-options="{{ $product->options()->exists() ? 1 : 0 }}"
                                        >Add to Order</a>
                                    @else
                                    <a href="javascript:void(0)" class="btn btn-outline-dark fw-bold btn-rounded">Out of Stock</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
        @if (Route::is('home'))
            <div class="text-center mb-5">
                <a href="{{ route('menu') }}" class="btn btn-outline-dark rounded-pill px-4 py-2">View Full Menu</a>
            </div>
        @endif
    </div>
</section>