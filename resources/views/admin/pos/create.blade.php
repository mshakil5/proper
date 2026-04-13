@extends('admin.pages.master')
@section('title', 'POS — Point of Sale')

@section('content')
<style>
    .pos-wrapper {
        display: grid;
        grid-template-columns: 1fr 420px;
        gap: 0;
        height: calc(100vh - 60px);
        background: #f4f6fa;
        transition: grid-template-columns .3s ease;
    }
    .pos-wrapper.panel-collapsed {
        grid-template-columns: 1fr 52px;
    }

    .pos-left {
        display: flex;
        flex-direction: column;
        overflow: hidden;
        padding: 16px;
        gap: 12px;
    }
    .cat-pills { display:flex; gap:8px; flex-wrap:wrap; }
    .cat-pill {
        padding:7px 18px; border-radius:30px; border:1.5px solid #e5e7eb;
        background:#fff; font-size:13px; font-weight:600; cursor:pointer; transition:all .15s; white-space:nowrap;
    }
    .cat-pill.active { background:#ff5a00; color:#fff; border-color:#ff5a00; }
    .products-scroll { flex:1; overflow-y:auto; }
    .products-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(150px,1fr)); gap:12px; padding-bottom:10px; }
    .product-tile {
        background:#fff; border-radius:14px; border:2px solid #f0f0f0;
        cursor:pointer; transition:all .15s; overflow:hidden;
    }
    .product-tile:hover { border-color:#ff5a00; transform:translateY(-2px); box-shadow:0 4px 16px rgba(255,90,0,.15); }
    .product-tile:active { transform:scale(.97); }
    .product-tile img { width:100%; height:110px; object-fit:cover; }
    .product-tile-body { padding:8px 10px 10px; }
    .product-tile-name { font-size:13px; font-weight:700; color:#111; line-height:1.3; margin-bottom:4px; }
    .product-tile-price { font-size:14px; font-weight:800; color:#ff5a00; }
    .product-tile-badge { font-size:10px; background:#fff3ee; color:#ff5a00; padding:2px 7px; border-radius:20px; display:inline-block; margin-bottom:4px; }

    .pos-right {
        background:#fff; border-left:1px solid #e5e7eb;
        display:flex; flex-direction:column; overflow:hidden; position:relative;
    }
    .pos-collapse-btn {
        position:absolute; top:14px; left:0px; z-index:10;
        width:32px; height:32px; border-radius:50%;
        background:#fff; border:1.5px solid #e5e7eb;
        display:flex; align-items:center; justify-content:center;
        cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,.12);
        font-size:14px; transition:all .2s;
    }
    .pos-collapse-btn:hover { background:#ff5a00; color:#fff; border-color:#ff5a00; }
    .panel-collapsed .pos-right-content { display:none; }
    .pos-right-collapsed-label {
        display:none; writing-mode:vertical-rl; text-orientation:mixed;
        font-size:13px; font-weight:700; color:#666; padding:20px 0;
        align-items:center; justify-content:center; cursor:pointer; flex:1;
    }
    .panel-collapsed .pos-right-collapsed-label { display:flex; }
    .pos-right-content { display:flex; flex-direction:column; overflow-y:auto; height:100%; }

    .pos-panel-header { padding:16px 20px 12px; border-bottom:1px solid #f0f0f0; flex-shrink:0; }
    .pos-panel-header h5 { font-size:16px; font-weight:800; margin:0 0 10px; }
    .pos-type-btns { display:grid; grid-template-columns:1fr 1fr; gap:6px; margin-bottom:10px; }
    .pos-type-btn {
        padding:10px; border-radius:10px; border:2px solid #e5e7eb; background:#fff;
        font-size:13px; font-weight:700; cursor:pointer; text-align:center; transition:all .15s;
    }
    .pos-type-btn.active { background:#111; color:#fff; border-color:#111; }
    .pos-type-btn i { display:block; font-size:18px; margin-bottom:2px; }
    .pos-field {
        width:100%; border:1.5px solid #e5e7eb; border-radius:10px;
        padding:9px 12px; font-size:13px; outline:none; margin-bottom:8px;
    }
    .pos-field:focus { border-color:#ff5a00; }
    .customer-row { display:flex; gap:8px; align-items:center; margin-bottom:8px; }
    .customer-row select { flex:1; }
    .btn-new-customer {
        padding:8px 12px; border-radius:10px; background:#111; color:#fff;
        border:none; font-size:12px; font-weight:700; cursor:pointer; white-space:nowrap;
    }

    .pos-panel-section { border-bottom:1px solid #f0f0f0; flex-shrink:0; }
    .pos-panel-section-header {
        padding:10px 20px; display:flex; align-items:center; justify-content:space-between;
        cursor:pointer; background:#fafafa; user-select:none;
    }
    .pos-panel-section-header:hover { background:#f0f0f0; }
    .pos-panel-section-title { font-size:13px; font-weight:700; color:#333; }
    .pos-panel-section-icon { font-size:12px; color:#999; transition:transform .2s; }
    .pos-panel-section-icon.open { transform:rotate(180deg); }
    .pos-panel-section-body { padding:12px 20px; }
    .pos-panel-section-body.hidden { display:none; }

    .pos-order-items { overflow-y:auto; padding:12px 16px; max-height:220px; }
    .order-item-row {
        display:flex; align-items:center; gap:10px;
        padding:10px 12px; background:#f8f9fa; border-radius:12px; margin-bottom:8px;
    }
    .order-item-img { width:44px; height:44px; border-radius:8px; object-fit:cover; flex-shrink:0; }
    .order-item-info { flex:1; min-width:0; }
    .order-item-name { font-size:13px; font-weight:700; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .order-item-opts { font-size:11px; color:#888; line-height:1.4; }
    .order-item-price { font-size:13px; font-weight:800; color:#ff5a00; }
    .qty-ctrl { display:flex; align-items:center; gap:4px; flex-shrink:0; }
    .qty-btn {
        width:28px; height:28px; border-radius:8px; border:1.5px solid #e5e7eb;
        background:#fff; font-size:16px; line-height:1; cursor:pointer;
        display:flex; align-items:center; justify-content:center; font-weight:700;
    }
    .qty-btn:hover { background:#ff5a00; color:#fff; border-color:#ff5a00; }
    .qty-val { font-size:14px; font-weight:800; min-width:20px; text-align:center; }
    .remove-btn {
        width:28px; height:28px; border-radius:8px; border:none;
        background:#fee2e2; color:#dc2626; cursor:pointer;
        display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0;
    }

    .pos-footer { padding:14px 16px; border-top:1px solid #f0f0f0; flex-shrink:0; position:sticky; bottom:0; background:#fff; z-index:5; }
    .pos-summary-row { display:flex; justify-content:space-between; font-size:13px; color:#666; margin-bottom:4px; }
    .pos-summary-row.discount { color:#28a745; }
    .pos-total-row { display:flex; justify-content:space-between; font-size:20px; font-weight:800; color:#111; margin:8px 0 12px; }
    .btn-place {
        width:100%; height:52px; background:#ff5a00; color:#fff; border:none;
        border-radius:14px; font-size:16px; font-weight:800; cursor:pointer; transition:all .15s;
    }
    .btn-place:hover { background:#e04e00; }
    .btn-place:disabled { background:#ccc; cursor:not-allowed; }
    .btn-clear {
        background:none; border:1.5px solid #e5e7eb; border-radius:10px;
        padding:6px 14px; font-size:13px; font-weight:600; cursor:pointer;
    }
    .btn-clear:hover { border-color:#dc2626; color:#dc2626; }

    .pos-empty { text-align:center; color:#bbb; padding:40px 20px; }
    .pos-empty i { font-size:48px; display:block; margin-bottom:10px; }

    .pos-promo-row { display:flex; gap:6px; margin-bottom:8px; }
    .pos-promo-row input { flex:1; }
    .btn-pos-promo {
        padding:9px 14px; background:#111; color:#fff; border:none;
        border-radius:10px; font-size:12px; font-weight:700; cursor:pointer; white-space:nowrap;
    }
    .pos-points-box { background:#f8f9fa; border-radius:10px; padding:10px 12px; margin-top:6px; }
    .pos-points-row { display:flex; justify-content:space-between; font-size:12px; color:#666; margin-bottom:3px; }

    .pos-modal-overlay {
        position:fixed; inset:0; background:rgba(0,0,0,.5);
        z-index:9999; display:flex; align-items:center; justify-content:center;
    }
    .pos-modal {
        background:#fff; border-radius:20px; width:460px;
        max-width:95vw; max-height:90vh; overflow-y:auto; padding:24px;
    }
    .pos-modal-title { font-size:18px; font-weight:800; margin-bottom:16px; }
    .pos-option-group { margin-bottom:16px; }
    .option-group-title {
        font-size:13px; font-weight:700; color:#555; margin-bottom:8px;
        display:flex; justify-content:space-between;
    }
    .option-item {
        display:flex; align-items:center; gap:10px;
        padding:10px 12px; border:2px solid #f0f0f0; border-radius:10px;
        margin-bottom:6px; cursor:pointer; transition:all .15s;
    }
    .option-item:hover { border-color:#ff5a00; }
    .option-item.selected { border-color:#ff5a00; background:#fff8f5; }
    .option-item-name { flex:1; font-size:14px; font-weight:600; }
    .option-item-price { font-size:13px; color:#ff5a00; font-weight:700; }
    .attr-btns { display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:16px; }
    .attr-btn {
        padding:12px; border:2px solid #e5e7eb; border-radius:12px;
        text-align:center; cursor:pointer; font-weight:700; font-size:13px; transition:all .15s;
    }
    .attr-btn.selected { border-color:#ff5a00; background:#fff8f5; color:#ff5a00; }
    .qty-row { display:flex; align-items:center; gap:12px; justify-content:center; margin:16px 0; }
    .qty-row .qty-btn { width:38px; height:38px; font-size:20px; }
    .qty-row .qty-val { font-size:22px; font-weight:800; min-width:40px; }
    .btn-add-to-order {
        width:100%; height:48px; background:#ff5a00; color:#fff; border:none;
        border-radius:12px; font-size:15px; font-weight:800; cursor:pointer; margin-top:8px;
    }
    .option-error { border:2px solid #dc2626 !important; border-radius:10px; }
    .form-row-2 { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
</style>

<div class="pos-wrapper" id="posWrapper">

    <div class="pos-left">
        <input type="text" id="posSearch" placeholder="🔍  Search products..."
            style="width:100%;height:46px;border-radius:12px;border:1.5px solid #e5e7eb;padding:0 16px;font-size:15px;outline:none;">

        <div class="cat-pills" id="catPills">
            <div class="cat-pill active" data-cat="all">All</div>
            @foreach ($categories as $cat)
                <div class="cat-pill" data-cat="{{ $cat->id }}">{{ $cat->name }}</div>
            @endforeach
        </div>

        <div class="products-scroll">
            <div class="products-grid" id="productsGrid">
                @foreach ($categories as $category)
                    @foreach ($category->products as $product)
                        <div class="product-tile"
                            data-cat="{{ $category->id }}"
                            data-name="{{ strtolower($product->title) }}"
                            data-id="{{ $product->id }}"
                            data-has-options="{{ $product->options()->exists() ? 1 : 0 }}">
                            <img src="{{ asset($product->image ?? '/placeholder.webp') }}" alt="{{ $product->title }}">
                            <div class="product-tile-body">
                                @if ($product->options()->exists())
                                    <div class="product-tile-badge">+ Options</div>
                                @endif
                                <div class="product-tile-name">{{ $product->title }}</div>
                                <div class="product-tile-price">£{{ number_format($product->price, 2) }}</div>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
            <div id="noResults" style="display:none;" class="pos-empty">
                <i class="ri-search-line"></i>No products found
            </div>
        </div>
    </div>

    <div class="pos-right">
        <div class="pos-collapse-btn" id="collapseBtn" title="Toggle order panel">‹</div>
        <div class="pos-right-collapsed-label" id="expandLabel">🧾 Order</div>

        <div class="pos-right-content" id="posRightContent">

            <div class="pos-panel-header">
                <div class="d-flex justify-content-between align-items-center mb-2 px-5">
                    <h5>🧾 Current Order</h5>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge bg-secondary" id="itemCountBadge">0 items</span>
                        <button class="btn-clear" id="clearOrderBtn">Clear</button>
                    </div>
                </div>

                <div class="pos-type-btns">
                    <div class="pos-type-btn active" data-type="collection">
                        <i class="ri-walk-line"></i> Collection
                    </div>
                    <div class="pos-type-btn" data-type="delivery">
                        <i class="ri-e-bike-2-line"></i> Delivery
                    </div>
                </div>

                <select class="pos-field" id="posTimeSlot">
                    <option value="">⏰ Select Time Slot...</option>
                </select>

                <div class="customer-row">
                    <select class="pos-field" id="posCustomer" style="margin-bottom:0;">
                        <option value="">— Walk-in Customer —</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}"
                                data-name="{{ $client->name }}"
                                data-phone="{{ $client->phone }}"
                                data-email="{{ $client->email }}"
                                data-points="{{ $client->available_points ?? 0 }}">
                                {{ $client->name }} — {{ $client->phone }}
                            </option>
                        @endforeach
                    </select>
                    <button class="btn-new-customer" id="openNewCustomerBtn">+ New</button>
                </div>

                <div id="deliveryAddressSection" style="display: none; margin-top: 12px; padding-top: 12px; border-top: 1px solid #f0f0f0;">
                <div class="pos-field-wrapper">
                    <label style="font-size:12px;font-weight:700;color:#555;margin-bottom:4px;display:block;">Postcode <span style="color:#dc2626;">*</span></label>
                    <input type="text" class="pos-field" id="deliveryPostcode" placeholder="e.g. LN5 8LQ" style="margin-bottom:8px;">
                </div>

                <div class="pos-field-wrapper">
                    <label style="font-size:12px;font-weight:700;color:#555;margin-bottom:4px;display:block;">Street Address <span style="color:#dc2626;">*</span></label>
                    <input type="text" class="pos-field" id="deliveryAddress" placeholder="123 Main Street" style="margin-bottom:8px;">
                </div>

                <div class="form-row-2">
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#555;margin-bottom:4px;display:block;">City <span style="color:#dc2626;">*</span></label>
                        <input type="text" class="pos-field" id="deliveryCity" placeholder="Lincoln" style="margin-bottom:0;">
                    </div>
                    <div>
                        <label style="font-size:12px;font-weight:700;color:#555;margin-bottom:4px;display:block;">Address Line 2 (Optional)</label>
                        <input type="text" class="pos-field" id="deliveryAddress2" placeholder="Flat 4 / Apartment" style="margin-bottom:0;">
                    </div>
                </div>
            </div>

            <div class="pos-panel-section">
                <div class="pos-panel-section-header" id="orderItemsToggle">
                    <span class="pos-panel-section-title">🛒 Order Items</span>
                    <span class="pos-panel-section-icon open" id="orderItemsIcon">▼</span>
                </div>
                <div class="pos-panel-section-body" id="orderItemsBody" style="padding:0;">
                    <div class="pos-order-items" id="orderItemsList">
                        <div class="pos-empty"><i class="ri-shopping-basket-line"></i>Tap a product to add</div>
                    </div>
                </div>
            </div>

            <div class="pos-panel-section d-none">
                <div class="pos-panel-section-header" id="promoToggle">
                    <span class="pos-panel-section-title">🎟 Promo / Gift Card</span>
                    <span class="pos-panel-section-icon" id="promoIcon">▼</span>
                </div>
                <div class="pos-panel-section-body hidden" id="promoBody">
                    <div class="pos-promo-row">
                        <input type="text" class="pos-field" id="posPromoCode" placeholder="Enter code..." style="margin-bottom:0;text-transform:uppercase;">
                        <button class="btn-pos-promo" id="applyPromoBtn">Apply</button>
                    </div>
                    <div id="promoResultMsg" style="display:none;font-size:12px;font-weight:600;margin-top:4px;padding:6px 10px;border-radius:8px;"></div>
                </div>
            </div>

            <div class="pos-panel-section" id="pointsSection" style="display:none;">
                <div class="pos-panel-section-header" id="pointsToggle">
                    <span class="pos-panel-section-title">⭐ Redeem Points</span>
                    <span class="pos-panel-section-icon" id="pointsIcon">▼</span>
                </div>
                <div class="pos-panel-section-body hidden" id="pointsBody">
                    <div class="pos-points-box" id="pointsInfoBox">
                        <div class="pos-points-row">
                            <span>Available:</span><strong id="availablePointsDisplay">0</strong>
                        </div>
                        <div class="pos-points-row"><span>100 pts = £1</span></div>
                    </div>
                    <div style="display:flex;gap:6px;margin-top:8px;">
                        <input type="number" class="pos-field" id="posPointsToUse" min="0" value="0" placeholder="Points to redeem" style="margin-bottom:0;">
                        <button class="btn-pos-promo" id="applyPointsBtn">Apply</button>
                    </div>
                    <div class="pos-points-box" style="margin-top:8px;">
                        <div class="pos-points-row"><span>Points used:</span><strong id="pointsUsedDisplay">0</strong></div>
                        <div class="pos-points-row"><span>Discount:</span><strong id="pointsDiscountDisplay" style="color:#28a745;">£0.00</strong></div>
                    </div>
                </div>
            </div>

            <div class="pos-panel-section">
                <div class="pos-panel-section-header" id="notesToggle">
                    <span class="pos-panel-section-title">📝 Order Notes</span>
                    <span class="pos-panel-section-icon" id="notesIcon">▼</span>
                </div>
                <div class="pos-panel-section-body hidden" id="notesBody">
                    <textarea class="pos-field" id="posNotes" rows="2" placeholder="Special instructions..." style="resize:none;margin-bottom:0;"></textarea>
                </div>
            </div>

            <div class="pos-panel-section">
                <div class="pos-panel-section-header" style="cursor:default;">
                    <span class="pos-panel-section-title">💳 Payment Method</span>
                </div>
                <div class="pos-panel-section-body" id="paymentBody">
                    <div style="display:flex;flex-direction:column;gap:6px;">
                        <label style="display:flex;align-items:center;gap:10px;padding:10px;border:2px solid #e5e7eb;border-radius:10px;cursor:pointer;transition:all .15s;" id="payCashLabel">
                            <input type="radio" name="posPaymentMethod" value="cash" id="posCash" checked style="accent-color:#ff5a00;">
                            <span style="font-size:13px;font-weight:700;">💵 Cash on Delivery</span>
                        </label>
                        {{-- <label style="display:flex;align-items:center;gap:10px;padding:10px;border:2px solid #e5e7eb;border-radius:10px;cursor:pointer;transition:all .15s;" id="payStripeLabel">
                            <input type="radio" name="posPaymentMethod" value="stripe" id="posStripe" style="accent-color:#ff5a00;">
                            <span style="font-size:13px;font-weight:700;">💳 Stripe</span>
                        </label>
                        <label style="display:flex;align-items:center;gap:10px;padding:10px;border:2px solid #e5e7eb;border-radius:10px;cursor:pointer;transition:all .15s;" id="payPaypalLabel">
                            <input type="radio" name="posPaymentMethod" value="paypal" id="posPaypal" style="accent-color:#ff5a00;">
                            <span style="font-size:13px;font-weight:700;"><i class="fab fa-paypal"></i> PayPal</span>
                        </label> --}}
                    </div>
                </div>
            </div>

            <div class="pos-footer">
                <div class="pos-summary-row"><span>Subtotal</span><span id="posSubtotal">£0.00</span></div>
                <div class="pos-summary-row" id="posDeliveryRow" style="display:none;"><span>Delivery</span><span id="posDeliveryCharge">£0.00</span></div>
                <div class="pos-summary-row discount" id="posPromoRow" style="display:none;"><span>Promo Discount</span><span id="posPromoDiscount">-£0.00</span></div>
                <div class="pos-summary-row discount" id="posPointsRow" style="display:none;"><span>Points Discount</span><span id="posPointsDiscount">-£0.00</span></div>
                <div class="pos-total-row"><span>Total</span><span id="posTotal">£0.00</span></div>
                <button class="btn-place" id="placeOrderBtn" disabled>✅ Place Order</button>
            </div>

        </div>
    </div>
</div>

<div id="productModal" style="display:none;" class="pos-modal-overlay">
    <div class="pos-modal" id="productModalInner"></div>
</div>

<div id="newCustomerModal" style="display:none;" class="pos-modal-overlay">
    <div class="pos-modal">
        <div class="pos-modal-title">➕ Quick Add Customer</div>
        <div class="form-row-2">
            <div>
                <label style="font-size:12px;font-weight:700;color:#555;">First Name *</label>
                <input class="pos-field" id="ncFirstName" placeholder="John">
            </div>
            <div>
                <label style="font-size:12px;font-weight:700;color:#555;">Last Name *</label>
                <input class="pos-field" id="ncLastName" placeholder="Doe">
            </div>
        </div>
        <label style="font-size:12px;font-weight:700;color:#555;">Email *</label>
        <input class="pos-field" id="ncEmail" type="email" placeholder="john@example.com">
        <label style="font-size:12px;font-weight:700;color:#555;">Phone *</label>
        <input class="pos-field" id="ncPhone" placeholder="07700000000">
        <label style="font-size:12px;font-weight:700;color:#555;">Password</label>
        <input class="pos-field" id="ncPassword" type="password" value="Password123!">
        <div style="display:flex;gap:10px;margin-top:8px;">
            <button id="cancelNewCustomerBtn" style="flex:1;padding:12px;border-radius:10px;border:1.5px solid #e5e7eb;background:#fff;font-weight:700;cursor:pointer;">Cancel</button>
            <button id="saveNewCustomerBtn" style="flex:1;padding:12px;border-radius:10px;border:none;background:#ff5a00;color:#fff;font-weight:800;cursor:pointer;">Save Customer</button>
        </div>
    </div>
</div>

<div id="successModal" style="display:none;" class="pos-modal-overlay">
    <div class="pos-modal" style="text-align:center;">
        <div style="font-size:56px;margin-bottom:8px;">✅</div>
        <div style="font-size:20px;font-weight:800;margin-bottom:4px;">Order Placed!</div>
        <div style="font-size:13px;color:#888;margin-bottom:12px;">Order Number</div>
        <div style="font-size:28px;font-weight:900;color:#ff5a00;margin-bottom:20px;" id="successOrderNum">—</div>
        <button id="newOrderBtn" style="width:100%;height:48px;background:#111;color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:800;cursor:pointer;">🛒 New Order</button>
    </div>
</div>

@endsection

@section('script')
<script>
    $(function () {

        const SHOP_HOURS = {
            Monday:    { open:'16:30', close:'23:30' },
            Tuesday:   { open:'16:30', close:'23:30' },
            Wednesday: { open:'16:30', close:'23:30' },
            Thursday:  { open:'16:30', close:'23:30' },
            Friday:    { open:'16:30', close:'23:30' },
            Saturday:  { open:'16:30', close:'23:30' },
            Sunday:    { open:'16:30', close:'22:00' },
        };

        let deliveryType    = 'collection';
        let cart            = [];
        let appliedPromo    = { type: null, id: null, discount: 0, code: null };
        let pointsUsed      = 0;
        let pointsDiscount  = 0;
        let selectedCustomer = { id: null, name: '', email: '', phone: '', points: 0 };
        let deliveryCharge  = 0;

        const wrapper  = document.getElementById('posWrapper');
        const colBtn   = document.getElementById('collapseBtn');
        const expLabel = document.getElementById('expandLabel');
        let collapsed  = false;

        colBtn.addEventListener('click', togglePanel);
        expLabel.addEventListener('click', togglePanel);

        function togglePanel() {
            collapsed = !collapsed;
            wrapper.classList.toggle('panel-collapsed', collapsed);
            colBtn.innerHTML = collapsed ? '›' : '‹';
        }

        function makeCollapsible(toggleId, bodyId, iconId) {
            $('#' + toggleId).on('click', function () {
                const body = $('#' + bodyId);
                const icon = $('#' + iconId);
                body.toggleClass('hidden');
                icon.toggleClass('open');
            });
        }
        makeCollapsible('orderItemsToggle', 'orderItemsBody', 'orderItemsIcon');
        makeCollapsible('promoToggle',   'promoBody',   'promoIcon');
        makeCollapsible('pointsToggle',  'pointsBody',  'pointsIcon');
        makeCollapsible('notesToggle',   'notesBody',   'notesIcon');

        $(document).on('change', 'input[name="posPaymentMethod"]', function () {
            $('input[name="posPaymentMethod"]').each(function () {
                $(this).closest('label').css('border-color', '#e5e7eb');
            });
            $(this).closest('label').css('border-color', '#ff5a00');
        });
        $('#posCash').closest('label').css('border-color', '#ff5a00');

        function generateTimeSlots() {
            const now     = new Date();
            const dayName = now.toLocaleDateString('en-GB', { weekday:'long' });
            const hours   = SHOP_HOURS[dayName];
            const [openH, openM]   = hours.open.split(':').map(Number);
            const [closeH, closeM] = hours.close.split(':').map(Number);

            let cursor = new Date(now.getFullYear(), now.getMonth(), now.getDate(), openH, openM);
            const close = new Date(now.getFullYear(), now.getMonth(), now.getDate(), closeH, closeM);

            if (now > cursor) {
                const rounded = Math.ceil(now.getMinutes() / 20) * 20;
                if (rounded >= 60) {
                    cursor = new Date(now.getFullYear(), now.getMonth(), now.getDate(), now.getHours() + 1, 0);
                } else {
                    cursor = new Date(now.getFullYear(), now.getMonth(), now.getDate(), now.getHours(), rounded);
                }
            }

            const slots = [];
            const fmt = d => d.toLocaleTimeString('en-GB', { hour:'2-digit', minute:'2-digit', hour12:true });

            while (cursor < close) {
                const end = new Date(cursor.getTime() + 20 * 60000);
                if (end > close) break;
                slots.push({ value: fmt(cursor) + '-' + fmt(end), label: fmt(cursor) + ' – ' + fmt(end) });
                cursor = end;
            }
            return slots;
        }

        function populateSlots() {
            const slots = generateTimeSlots();
            let html = '<option value="">⏰ Select Time Slot...</option>';
            slots.forEach(s => { html += `<option value="${s.value}">${s.label}</option>`; });
            $('#posTimeSlot').html(html);
        }
        populateSlots();

        $(document).on('click', '.cat-pill', function () {
            $('.cat-pill').removeClass('active');
            $(this).addClass('active');
            filterProducts();
        });

        $('#posSearch').on('input', filterProducts);

        function filterProducts() {
            const q   = $('#posSearch').val().toLowerCase().trim();
            const cat = $('.cat-pill.active').data('cat');
            let visible = 0;
            $('.product-tile').each(function () {
                const matchCat = cat === 'all' || $(this).data('cat') == cat;
                const matchQ   = !q || $(this).data('name').includes(q);
                $(this).toggle(matchCat && matchQ);
                if (matchCat && matchQ) visible++;
            });
            $('#noResults').toggle(visible === 0);
        }

        $(document).on('click', '.pos-type-btn', function () {
            deliveryType = $(this).data('type');
            $('.pos-type-btn').removeClass('active');
            $(this).addClass('active');

            if (deliveryType === 'delivery') {
                $('#deliveryAddressSection').slideDown(200);
                deliveryCharge = 2.50;
            } else {
                $('#deliveryAddressSection').slideUp(200);
                deliveryCharge = 0;
            }

            $('#posDeliveryRow').toggle(deliveryType === 'delivery');
            updateTotals();
        });

        $('#posCustomer').on('change', function () {
            const selected = $(this).find('option:selected');
            const id       = $(this).val();

            if (!id) {
                selectedCustomer = { id: null, name: '', email: '', phone: '', points: 0 };
                $('#pointsSection').hide();
                resetPoints();
                return;
            }

            selectedCustomer = {
                id:     id,
                name:   selected.data('name') || '',
                email:  selected.data('email') || '',
                phone:  selected.data('phone') || '',
                points: parseInt(selected.data('points')) || 0,
            };

            if (selectedCustomer.points >= 100) {
                $('#pointsSection').show();
                $('#availablePointsDisplay').text(selectedCustomer.points);
                $('#posPointsToUse').attr('max', selectedCustomer.points);
            } else {
                $('#pointsSection').hide();
                resetPoints();
            }
        });

        function resetPoints() {
            pointsUsed     = 0;
            pointsDiscount = 0;
            $('#posPointsToUse').val(0);
            $('#pointsUsedDisplay').text(0);
            $('#pointsDiscountDisplay').text('£0.00');
            $('#posPointsRow').hide();
            updateTotals();
        }

        $('#applyPromoBtn').on('click', function () {
            const code = $('#posPromoCode').val().trim().toUpperCase();
            if (!code) { showError('Enter a promo code'); return; }

            const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
            if (subtotal <= 0) { showError('Add items first'); return; }

            $.ajax({
                url: '{{ route('admin.pos.validate-promo') }}',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: { code: code, subtotal: subtotal, customer_id: selectedCustomer.id || '' },
                success: function (res) {
                    let actualDiscount = res.discount_amount;
                    const remaining = subtotal + deliveryCharge - pointsDiscount;
                    if (res.type === 'gift_card') actualDiscount = Math.min(actualDiscount, remaining);

                    appliedPromo = { type: res.type, id: res.code_data.id, discount: actualDiscount, code: code };

                    const msg = res.type === 'gift_card'
                        ? `✓ Gift Card applied! -£${actualDiscount.toFixed(2)}`
                        : `✓ Coupon applied! -£${actualDiscount.toFixed(2)}`;

                    $('#promoResultMsg').text(msg).css({ display:'block', background:'#f0fdf4', color:'#166534' });
                    $('#posPromoRow').show();
                    $('#posPromoDiscount').text('-£' + actualDiscount.toFixed(2));
                    updateTotals();
                    showSuccess('Promo applied!');
                },
                error: function (xhr) {
                    appliedPromo = { type: null, id: null, discount: 0, code: null };
                    $('#posPromoRow').hide();
                    const msg = xhr.responseJSON?.message || 'Invalid code';
                    $('#promoResultMsg').text('✗ ' + msg).css({ display:'block', background:'#fef2f2', color:'#dc2626' });
                    updateTotals();
                    showError(msg);
                }
            });
        });

        $('#applyPointsBtn').on('click', function () {
            let val        = parseInt($('#posPointsToUse').val()) || 0;
            const maxPts   = selectedCustomer.points;
            const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
            const remaining = subtotal + deliveryCharge - appliedPromo.discount;
            const maxByTotal = Math.floor(remaining * 100);

            val = Math.min(val, maxPts, maxByTotal);
            val = Math.max(val, 0);

            $('#posPointsToUse').val(val);
            pointsUsed     = val;
            pointsDiscount = Math.round(val / 100 * 100) / 100;

            $('#pointsUsedDisplay').text(val);
            $('#pointsDiscountDisplay').text('£' + pointsDiscount.toFixed(2));

            if (pointsDiscount > 0) {
                $('#posPointsRow').show();
                $('#posPointsDiscount').text('-£' + pointsDiscount.toFixed(2));
            } else {
                $('#posPointsRow').hide();
            }
            updateTotals();
            showSuccess('Points applied!');
        });

        $(document).on('click', '.product-tile', function () {
            const id        = $(this).data('id');
            const hasOptions = $(this).data('has-options') == 1;

            $.ajax({
                url: '{{ route('admin.pos.product') }}',
                type: 'GET',
                data: { id: id },
                success: function (res) {
                    if (!hasOptions) {
                        addToCartSimple({ id: id, title: res.title, price: parseFloat(res.price), image: res.image, sku_ref: res.sku_ref }, 1);
                    } else {
                        $('#productModalInner').html(res.html);
                        $('#productModal').show();
                        updatePosModalPrice();
                        
                    }
                },
                error: function () { showError('Failed to load product'); }
            });
        });

        $('#productModal').on('click', function (e) {
            if (e.target === this) closePosProductModal();
        });

        window.closePosProductModal = function () {
            $('#productModal').hide();
            $('#productModalInner').html('');
        };

        $(document).on('click', '.attr-btn', function () {
            $('.attr-btn').removeClass('selected');
            $(this).addClass('selected');
            if ($(this).data('attr') === 'with_options') {
                $('#posOptionsContainer').slideDown();
            } else {
                $('#posOptionsContainer').slideUp();
                $('#posOptionsContainer .option-item').removeClass('selected').find('input').prop('checked', false);
            }
            updatePosModalPrice();
        });

        $(document).on('click', '.option-item', function () {
            const group = $(this).closest('.pos-option-group');
            const input = $(this).find('input')[0];

            group.find('.option-item').removeClass('selected');
            group.find('input').prop('checked', false);

            $(this).addClass('selected');
            input.checked = true;

            const title = $('#productModalInner .pos-modal-title').text().toLowerCase();
            if (!title.includes('combo kebab')) return;

            let selected = [];

            $('#productModalInner input:checked').each(function () {
                selected.push($(this).val());
            });

            $('.pos-option-group').each(function () {
                $(this).find('.option-item').each(function () {
                    const val = $(this).find('input').val();

                    if (selected.includes(val) && !$(this).find('input').is(':checked')) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
            });

            updatePosModalPrice();
        });

        function updatePosModalPrice() {
            const basePrice     = parseFloat($('#posModalPrice').data('base-price')) || 0;
            const hasAttribute  = $('.attr-btn').length > 0;
            const attrSelected  = $('.attr-btn.selected').data('attr');
            let attributePrice  = 0;
            let extraPrice      = 0;

            if (hasAttribute && attrSelected === 'with_options') {
                attributePrice = parseFloat($('.attr-btn[data-attr="with_options"]').data('attr-price')) || 0;
                $('#posOptionsContainer input:checked').each(function () {
                    extraPrice += parseFloat($(this).data('price')) || 0;
                });
            } else if (!hasAttribute) {
                $('#posOptionsContainer input:checked').each(function () {
                    extraPrice += parseFloat($(this).data('price')) || 0;
                });
            }

            const qty   = parseInt($('#posModalQty').text()) || 1;
            const total = (basePrice + attributePrice + extraPrice) * qty;
            $('#posModalPrice').text(total.toFixed(2));
        }

        $(document).on('click', '#posModalQtyPlus', function () {
            let qty = parseInt($('#posModalQty').text()) || 1;
            $('#posModalQty').text(qty + 1);
            updatePosModalPrice();
        });

        $(document).on('click', '#posModalQtyMinus', function () {
            let qty = parseInt($('#posModalQty').text()) || 1;
            if (qty > 1) $('#posModalQty').text(qty - 1);
            updatePosModalPrice();
        });

        $(document).on('submit', '#posProductForm', function (e) {
            e.preventDefault();

            const productId    = parseInt($('#posProductId').val()) || null;
            const skuRef       = $('#posProductSkuRef').val() || '';
            if (!productId) return;

            const hasAttribute = $('.attr-btn').length > 0;
            const attrSelected = $('.attr-btn.selected').data('attr') || 'standalone';

            if (hasAttribute && !attrSelected) {
                showError('Please choose an option');
                return;
            }

            let valid        = true;
            let missingOpts  = [];
            const container  = (hasAttribute && attrSelected === 'with_options')
                ? $('#posOptionsContainer .pos-option-group')
                : (!hasAttribute ? $('.pos-option-group') : $());

            container.each(function () {
                if (parseInt($(this).data('required')) && !$(this).find('input:checked').length) {
                    missingOpts.push($(this));
                    valid = false;
                }
            });

            if (!valid) {
                $('.pos-option-group').removeClass('option-error');
                missingOpts.forEach(s => s.addClass('option-error'));
                if (missingOpts.length) missingOpts[0][0].scrollIntoView({ behavior:'smooth', block:'start' });
                return;
            }

            let options        = {};
            let extraPrice     = 0;
            let attributePrice = 0;
            const basePrice    = parseFloat($('#posModalPrice').data('base-price')) || 0;
            const productTitle = $('#posProductForm').closest('.pos-modal').find('.pos-modal-title').text().trim();
            const productImage = $('#posProductImage').attr('src') || '';

            if (hasAttribute && attrSelected === 'with_options') {
                attributePrice = parseFloat($('.attr-btn[data-attr="with_options"]').data('attr-price')) || 0;
                $('#posOptionsContainer input:checked').each(function () {
                    const name = $(this).attr('name');
                    const price = parseFloat($(this).data('price')) || 0;
                    extraPrice += price;
                    if (!options[name]) options[name] = [];
                    options[name].push({ title: $(this).data('title'), price: price, productId: $(this).data('product-id'), hubriseOptionRef: $(this).data('hubrise') || '' });
                });
            } else if (!hasAttribute) {
                $('#posOptionsContainer input:checked').each(function () {
                    const name = $(this).attr('name');
                    const price = parseFloat($(this).data('price')) || 0;
                    extraPrice += price;
                    if (!options[name]) options[name] = [];
                    options[name].push({ title: $(this).data('title'), price: price, productId: $(this).data('product-id'), hubriseOptionRef: $(this).data('hubrise') || '' });
                });
            }

            const qty        = parseInt($('#posModalQty').text()) || 1;
            const finalPrice = basePrice + extraPrice + attributePrice;
            const isStandalone = hasAttribute && attrSelected === 'standalone';

            if (isStandalone) {
                const existing = cart.find(i => i.productId === productId && i.type === 'direct_with_attribute');
                if (existing) { existing.qty += qty; }
                else {
                    cart.push({ productId, skuRef, title: productTitle, image: productImage, price: basePrice + attributePrice, qty, type:'direct_with_attribute', options:{}, attribute:true, attributePrice });
                }
                showSuccess('Added to order!');
            } else {
                const productHash = createProductHash(productId, productTitle, options, attributePrice);
                const existing    = cart.find(i => i.type === 'custom' && i.productHash === productHash && i.attributePrice === attributePrice);
                if (existing) { existing.qty += qty; showSuccess('Updated quantity!'); }
                else {
                    cart.push({ productId, skuRef, productHash, title: productTitle, image: productImage, price: finalPrice, qty, type:'custom', options, attribute: hasAttribute && attrSelected === 'with_options', attributePrice });
                    showSuccess('Added to order!');
                }
            }

            closePosProductModal();
            renderOrder();
        });

        function createProductHash(productId, title, options, attributePrice) {
            let optStr = '';
            if (Object.keys(options).length) {
                optStr = Object.keys(options).sort().map(k => k + ':' + options[k].map(o => o.title).sort().join('|')).join('||');
            }
            return productId + '::' + title + '::' + attributePrice + (optStr ? '::' + optStr : '');
        }

        function addToCartSimple(product, qty) {
            const existing = cart.find(i => i.productId === product.id && i.type === 'direct');
            if (existing) { existing.qty += qty; }
            else {
                cart.push({ productId: product.id, skuRef: product.sku_ref || '', title: product.title, image: product.image, price: parseFloat(product.price), qty, type:'direct', options:{}, attribute:false, attributePrice:0 });
            }
            showSuccess('Added to order!');
            renderOrder();
        }

        function renderOrder() {
            if (!cart.length) {
                $('#orderItemsList').html('<div class="pos-empty"><i class="ri-shopping-basket-line"></i>Tap a product to add</div>');
                updateTotals();
                return;
            }

            let html = '';
            cart.forEach((item, idx) => {
                let optsHtml = '';
                if (item.options && Object.keys(item.options).length) {
                    optsHtml = '<div class="order-item-opts">';
                    Object.values(item.options).forEach(arr => {
                        arr.forEach(o => { optsHtml += o.title + (o.price > 0 ? ` (+£${parseFloat(o.price).toFixed(2)})` : '') + '<br>'; });
                    });
                    optsHtml += '</div>';
                }
                html += `
                <div class="order-item-row">
                    <img src="${item.image}" class="order-item-img" alt="">
                    <div class="order-item-info">
                        <div class="order-item-name">${item.title}</div>
                        ${optsHtml}
                        <div class="order-item-price">£${(item.price * item.qty).toFixed(2)}</div>
                    </div>
                    <div class="qty-ctrl">
                        <button class="qty-btn cart-qty-minus" data-index="${idx}">−</button>
                        <span class="qty-val">${item.qty}</span>
                        <button class="qty-btn cart-qty-plus" data-index="${idx}">+</button>
                    </div>
                    <button class="remove-btn remove-item-btn" data-index="${idx}"><i class="ri-delete-bin-line"></i></button>
                </div>`;
            });

            $('#orderItemsList').html(html);
            updateTotals();
        }

        $(document).on('click', '.cart-qty-plus', function () {
            const idx = parseInt($(this).data('index'));
            cart[idx].qty += 1;
            renderOrder();
        });

        $(document).on('click', '.cart-qty-minus', function () {
            const idx = parseInt($(this).data('index'));
            if (cart[idx].qty > 1) { cart[idx].qty -= 1; renderOrder(); }
            else {
                showConfirm('Remove this item?', function () { cart.splice(idx, 1); renderOrder(); });
            }
        });

        $(document).on('click', '.remove-item-btn', function () {
            const idx = parseInt($(this).data('index'));
            showConfirm('Remove this item?', function () { cart.splice(idx, 1); renderOrder(); });
        });

        function updateTotals() {
            const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
            const count    = cart.reduce((s, i) => s + i.qty, 0);
            const total    = Math.max(0, subtotal + deliveryCharge - appliedPromo.discount - pointsDiscount);

            $('#posSubtotal').text('£' + subtotal.toFixed(2));
            $('#posTotal').text('£' + total.toFixed(2));
            $('#itemCountBadge').text(count + (count === 1 ? ' item' : ' items'));
            $('#placeOrderBtn').prop('disabled', cart.length === 0);

            if (deliveryCharge > 0) {
                $('#posDeliveryRow').show();
                $('#posDeliveryCharge').text('£' + deliveryCharge.toFixed(2));
            } else {
                $('#posDeliveryRow').hide();
            }
        }

        $('#clearOrderBtn').on('click', function () {
            cart           = [];
            appliedPromo   = { type:null, id:null, discount:0, code:null };
            pointsUsed     = 0;
            pointsDiscount = 0;
            deliveryCharge = 0;
            selectedCustomer = { id:null, name:'', email:'', phone:'', points:0 };
            $('#posCustomer').val('');
            $('#posNotes').val('');
            $('#posTimeSlot').val('');
            $('#posPromoCode').val('');
            $('#posPointsToUse').val(0);
            $('#promoResultMsg').hide();
            $('#posPromoRow, #posPointsRow, #posDeliveryRow, #pointsSection').hide();
            deliveryType = 'collection';
            $('.pos-type-btn').removeClass('active');
            $('.pos-type-btn[data-type="collection"]').addClass('active');
            renderOrder();
        });

        $('#placeOrderBtn').on('click', function () {
            if (!cart.length)  { showError('Add at least one item'); return; }
            if (!$('#posTimeSlot').val()) { showError('Please select a time slot'); return; }

            if (deliveryType === 'delivery') {
                const postcode = $('#deliveryPostcode').val().trim();
                const address  = $('#deliveryAddress').val().trim();
                const city     = $('#deliveryCity').val().trim();

                if (!postcode || !address || !city) {
                    showError('Please fill Postcode, Street Address and City for delivery');
                    $('#placeOrderBtn').prop('disabled', false).text('✅ Place Order');
                    return;
                }
            }

            const paymentMethod = $('input[name="posPaymentMethod"]:checked').val();
            if (!paymentMethod) { showError('Please select a payment method'); return; }

            let custFirstName = 'Walk-in', custLastName = 'Customer', custEmail = 'pos@internal.local', custPhone = '00000000000';
            let customerId    = null;

            if (selectedCustomer.id) {
                customerId    = selectedCustomer.id;
                const nameParts = (selectedCustomer.name || '').split(' ');
                custFirstName = nameParts[0] || 'Customer';
                custLastName  = nameParts.slice(1).join(' ') || 'POS';
                custEmail     = selectedCustomer.email || 'pos@internal.local';
                custPhone     = selectedCustomer.phone || '00000000000';
            }

            const orderData = {
                customer: {
                    firstName: custFirstName,
                    lastName:  custLastName,
                    email:     custEmail,
                    phone:     custPhone
                },
                customer_id: customerId,
                delivery: {
                    type: deliveryType,
                    time: $('#posTimeSlot').val(),
                    postcode: deliveryType === 'delivery' ? $('#deliveryPostcode').val().trim() : ''
                },
                address:  deliveryType === 'delivery' ? $('#deliveryAddress').val().trim() : '',
                address2: deliveryType === 'delivery' ? $('#deliveryAddress2').val().trim() : '',
                city:     deliveryType === 'delivery' ? $('#deliveryCity').val().trim() : '',
                cart: cart.map(i => ({
                    productId: i.productId,
                    quantity:  i.qty,
                    type:      i.type,
                    options:   i.options || null,
                    attribute: i.attribute || false,
                    attributePrice: i.attributePrice || 0,
                })),
                promoCode:     appliedPromo.code || null,
                pointsToUse:   pointsUsed,
                paymentMethod: paymentMethod,
                notes:         $('#posNotes').val().trim(),
            };

            $('#placeOrderBtn').prop('disabled', true).text('Placing...');

            $.ajax({
                url: '{{ route('admin.pos.place-order') }}',
                type: 'POST',
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: JSON.stringify(orderData),
                success: function (res) {
                    $('#placeOrderBtn').prop('disabled', false).text('✅ Place Order');
                    if (res.redirectUrl) {
                        window.location.href = res.redirectUrl;
                    } else {
                        $('#successOrderNum').text(res.orderNumber);
                        $('#successModal').show();
                    }
                },
                error: function (xhr) {
                    $('#placeOrderBtn').prop('disabled', false).text('✅ Place Order');
                    showError(xhr.responseJSON?.message || 'Error placing order');
                }
            });
        });

        $('#newOrderBtn').on('click', function () {
            $('#successModal').hide();
            $('#clearOrderBtn').click();
            populateSlots();
        });

        $('#openNewCustomerBtn').on('click', function () { $('#newCustomerModal').show(); });
        $('#cancelNewCustomerBtn').on('click', function () { $('#newCustomerModal').hide(); });
        $('#newCustomerModal').on('click', function (e) { if (e.target === this) $(this).hide(); });

        $('#saveNewCustomerBtn').on('click', function () {
            const data = {
                first_name: $('#ncFirstName').val().trim(),
                last_name:  $('#ncLastName').val().trim(),
                email:      $('#ncEmail').val().trim(),
                phone:      $('#ncPhone').val().trim(),
                password:   $('#ncPassword').val() || 'Password123!',
            };

            if (!data.first_name || !data.last_name || !data.email || !data.phone) {
                showError('Please fill all required fields');
                return;
            }

            $.ajax({
                url: '{{ route('admin.pos.quick-customer') }}',
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: data,
                success: function (res) {
                    const opt = `<option value="${res.id}" data-name="${res.name}" data-phone="${res.phone}" data-email="${res.email}" data-points="${res.available_points}" selected>
                        ${res.name} — ${res.phone}
                    </option>`;
                    $('#posCustomer').append(opt).val(res.id).trigger('change');
                    $('#newCustomerModal').hide();
                    $('#ncFirstName,#ncLastName,#ncEmail,#ncPhone').val('');
                    showSuccess('Customer created!');
                },
                error: function (xhr) {
                    showError(xhr.responseJSON?.message || 'Error creating customer');
                }
            });
        });

        renderOrder();
    });
</script>
@endsection