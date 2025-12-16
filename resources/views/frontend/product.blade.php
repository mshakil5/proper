<div class="product-modal-wrapper">
    <div class="product-modal-header">
        <img src="{{ asset($product->image ?? '/placeholder.webp') }}" id="productImage"
            class="product-image" alt="Product Image">
        @if($product->tag)
        <span class="product-badge">{{ $product->tag->name ?? '' }}</span>
        @endif
        <button type="button" data-bs-dismiss="modal" class="product-modal-close">&times;</button>
    </div>

    <div class="product-modal-body">
        <div class="product-title" id="productTitle">{{ $product->title ?? ''}}</div>
        <span class="product-category" id="productCategory">{{ $product->category->name ?? ''}}</span>
        <p class="product-description" id="productDescription">{{ $product->short_description ?? $product->long_description ?? ''}}</p>

        @forelse($product->options as $option)
            <div class="product-section">
                <div class="product-section-title">
                    <i class="fas fa-layer-group"></i> 
                    {{ $option->name }}
                    @if($option->is_required)
                        <span class="required">*</span>
                    @endif
                </div>

                @if($option->type === 'single')
                    <div class="option-group">
                        @foreach($option->items as $index => $item)
                            <div class="option-item {{ $index === 0 ? 'active' : '' }}">
                                <input 
                                    type="radio" 
                                    name="option_{{ $option->id }}" 
                                    value="{{ $item->product_id }}" 
                                    data-price="{{ $item->override_price }}"
                                    {{ $index === 0 ? 'checked' : '' }} 
                                    class="option-input"
                                    id="option_{{ $option->id }}_{{ $item->product_id }}"
                                    @if($option->is_required) required @endif>
                                <label for="option_{{ $option->id }}_{{ $item->product_id }}" class="option-label">
                                    {{ $item->product->title }}
                                </label>
                                @if($item->override_price > 0)
                                    <span class="option-price">+£{{ number_format($item->override_price, 2) }}</span>
                                @else
                                    <span class="option-price">+£0.00</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                @else
                    <div class="option-group">
                        @foreach($option->items as $item)
                            <div class="option-item">
                                <input 
                                    type="checkbox" 
                                    name="option_{{ $option->id }}[]" 
                                    value="{{ $item->product_id }}" 
                                    data-price="{{ $item->override_price }}"
                                    data-max-select="{{ $option->max_select }}"
                                    class="option-input option-checkbox-{{ $option->id }}"
                                    id="option_{{ $option->id }}_{{ $item->product_id }}"
                                    @if($option->is_required) required @endif>
                                <label for="option_{{ $option->id }}_{{ $item->product_id }}" class="option-label">
                                    {{ $item->product->title }}
                                </label>
                                @if($item->override_price > 0)
                                    <span class="option-price">+£{{ number_format($item->override_price, 2) }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
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
            <button class="qty-btn qty-minus">−</button>
            <input type="number" id="quantity" value="1" min="1" class="qty-input">
            <button class="qty-btn qty-plus">+</button>
        </div>
        <div class="price-section">
            <span class="price-label">Total Price</span>
            <span id="totalPrice" class="price-value">£{{ number_format($product->price, 2) }}</span>
        </div>
        <button class="btn-add-order">Add to Order</button>
    </div>
</div>