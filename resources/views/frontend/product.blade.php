<form id="productForm">
    <div class="product-modal-wrapper">
        <div class="product-modal-header">
            <img src="{{ asset($product->image ?? '/placeholder.webp') }}" id="productImage" class="product-image"
                alt="Product Image">
            @if ($product->tag)
                <span class="product-badge">{{ $product->tag->name ?? '' }}</span>
            @endif
            <button type="button" data-bs-dismiss="modal" class="product-modal-close">&times;</button>
        </div>

        <div class="product-modal-body">
            <div class="product-title" id="productTitle">{{ $product->title ?? '' }}</div>
            <span class="product-category" id="productCategory">{{ $product->category->name ?? '' }}</span>
            <p class="product-description" id="productDescription">
                {{ $product->short_description ?? ($product->long_description ?? '') }}</p>

            @forelse($product->options as $option)
                <div class="product-section" data-option-id="{{ $option->id }}"
                    data-required="{{ $option->is_required }}" data-max="{{ $option->max_select }}">
                    <div class="product-section-title">
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
                                    name="option_{{ $option->id }}{{ $option->type === 'multi' ? '[]' : '' }}"
                                    value="{{ $item->product_id }}" data-price="{{ $item->override_price }}"
                                    data-title="{{ $item->product->title }}" class="option-input"
                                    id="option_{{ $option->id }}_{{ $item->product_id }}"
                                    @if ($option->is_required && $option->type === 'single') required @endif
                                    @if ($option->type === 'multi' && $option->is_required) data-required="1" @endif>
                                <label for="option_{{ $option->id }}_{{ $item->product_id }}" class="option-label">
                                    {{ $item->product->title }}
                                </label>
                                @if ($item->override_price > 0)
                                    <span class="option-price">+£{{ number_format($item->override_price, 2) }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if ($option->type === 'multiple')
                        <small class="text-muted d-block mt-2">
                            Max selections: <strong>{{ $option->max_select }}</strong>
                        </small>
                    @endif
                </div>
            @empty
            @endforelse

        </div>

        <div class="product-modal-footer">
            <div class="quantity-control">
                <button type="button" class="qty-btn qty-minus">−</button>
                <input type="number" id="quantity" value="1" min="1" class="qty-input">
                <button type="button" class="qty-btn qty-plus">+</button>
            </div>
            <div class="price-section">
                <span class="price-label">Total Price</span>
                <span id="totalPrice" class="price-value"
                    data-base-price="{{ $product->price }}">£{{ number_format($product->price, 2) }}</span>
            </div>
            <button type="submit" class="btn-add-order">Add to Order</button>
        </div>
    </div>
</form>