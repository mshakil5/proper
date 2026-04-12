<form id="posProductForm">
    <input type="hidden" id="posProductId" value="{{ $product->id ?? '' }}">
    <input type="hidden" id="posProductSkuRef" value="{{ $product->sku_ref ?? '' }}">

    <div class="pos-modal-title">{{ $product->title ?? '' }}</div>

    <div style="text-align:center;margin-bottom:16px;">
        <img src="{{ asset($product->image ?? '/placeholder.webp') }}" id="posProductImage"
            style="width:100%;height:180px;object-fit:cover;border-radius:12px;" alt="{{ $product->title ?? '' }}">
        <div style="font-size:22px;font-weight:900;color:#ff5a00;margin-top:8px;">
            £<span id="posModalPrice" data-base-price="{{ $product->price ?? 0 }}">{{ number_format($product->price ?? 0, 2) }}</span>
        </div>
    </div>

    @if ($product->has_attribute)
        <div style="font-size:13px;font-weight:700;color:#555;margin-bottom:8px;">Choose Option *</div>
        <div class="attr-btns">
            <div class="attr-btn selected" data-attr="standalone">
                On Its Own<br>
                <span style="font-size:12px;color:#888;">£{{ number_format($product->price, 2) }}</span>
            </div>
            <div class="attr-btn" data-attr="with_options" data-attr-price="{{ $product->attribute_price }}">
                {{ $product->attribute_name }}<br>
                <span style="font-size:12px;color:#888;">£{{ number_format($product->price + $product->attribute_price, 2) }}</span>
            </div>
        </div>

        <div id="posOptionsContainer" style="display:none;">
            @forelse($product->options->sortBy('sort_order') as $option)
                <div class="pos-option-group"
                    data-option-id="{{ $option->id }}"
                    data-group="{{ $option->name }}"
                    data-required="{{ $option->is_required ? 1 : 0 }}"
                    data-max="{{ $option->type === 'single' ? 1 : $option->max_select }}"
                    data-type="{{ $option->type }}">
                    <div class="option-group-title">
                        <span>{{ $option->name }}@if($option->is_required)<span style="color:#ff5a00;"> *</span>@endif</span>
                        <span style="font-size:11px;">{{ $option->is_required ? '⚠️ Required' : 'Optional' }}@if($option->type !== 'single' && $option->max_select > 0) · Max {{ $option->max_select }}@endif</span>
                    </div>
                    @foreach($option->items->sortBy('override_price') as $item)
                        <div class="option-item" data-input-type="{{ $option->type === 'single' ? 'radio' : 'checkbox' }}">
                            <input type="{{ $option->type === 'single' ? 'radio' : 'checkbox' }}"
                                name="pos_opt_{{ $option->id }}"
                                value="{{ $item->product_id }}"
                                style="display:none;"
                                data-title="{{ $item->product->title ?? 'Unknown' }}"
                                data-price="{{ $item->override_price ?? 0 }}"
                                data-product-id="{{ $item->product_id }}"
                                data-hubrise="{{ $item->hubrise_option_ref ?? '' }}">
                            <span class="option-item-name">{{ $item->product->title ?? 'Unknown' }}</span>
                            @if(($item->override_price ?? 0) > 0)
                                <span class="option-item-price">+£{{ number_format($item->override_price, 2) }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @empty
            @endforelse
        </div>
    @else
        <div id="posOptionsContainer">
            @forelse($product->options->sortBy('sort_order') as $option)
                <div class="pos-option-group"
                    data-option-id="{{ $option->id }}"
                    data-group="{{ $option->name }}"
                    data-required="{{ $option->is_required ? 1 : 0 }}"
                    data-max="{{ $option->type === 'single' ? 1 : $option->max_select }}"
                    data-type="{{ $option->type }}">
                    <div class="option-group-title">
                        <span>{{ $option->name }}@if($option->is_required)<span style="color:#ff5a00;"> *</span>@endif</span>
                        <span style="font-size:11px;">{{ $option->is_required ? '⚠️ Required' : 'Optional' }}@if($option->type !== 'single' && $option->max_select > 0) · Max {{ $option->max_select }}@endif</span>
                    </div>
                    @foreach($option->items->sortBy('override_price') as $item)
                        <div class="option-item" data-input-type="{{ $option->type === 'single' ? 'radio' : 'checkbox' }}">
                            <input type="{{ $option->type === 'single' ? 'radio' : 'checkbox' }}"
                                name="pos_opt_{{ $option->id }}"
                                value="{{ $item->product_id }}"
                                style="display:none;"
                                data-title="{{ $item->product->title ?? 'Unknown' }}"
                                data-price="{{ $item->override_price ?? 0 }}"
                                data-product-id="{{ $item->product_id }}"
                                data-hubrise="{{ $item->hubrise_option_ref ?? '' }}">
                            <span class="option-item-name">{{ $item->product->title ?? 'Unknown' }}</span>
                            @if(($item->override_price ?? 0) > 0)
                                <span class="option-item-price">+£{{ number_format($item->override_price, 2) }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @empty
            @endforelse
        </div>
    @endif

    <div class="qty-row">
        <button type="button" class="qty-btn" id="posModalQtyMinus">−</button>
        <span class="qty-val" id="posModalQty">1</span>
        <button type="button" class="qty-btn" id="posModalQtyPlus">+</button>
    </div>

    <button type="submit" class="btn-add-to-order">Add to Order</button>
    <button type="button" onclick="closePosProductModal()"
        style="width:100%;margin-top:8px;padding:12px;border-radius:12px;border:1.5px solid #e5e7eb;background:#fff;font-weight:700;cursor:pointer;">
        Cancel
    </button>
</form>