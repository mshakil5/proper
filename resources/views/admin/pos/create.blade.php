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
        }

        .pos-left {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            padding: 16px;
            gap: 12px;
        }

        .pos-right {
            background: #fff;
            border-left: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }


        .pos-search-bar input {
            flex: 1;
            height: 46px;
            border-radius: 12px;
            border: 1.5px solid #e5e7eb;
            padding: 0 16px;
            font-size: 15px;
            outline: none;
        }

        .pos-search-bar input:focus {
            border-color: #ff5a00;
        }

        .cat-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .cat-pill {
            padding: 7px 18px;
            border-radius: 30px;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
        }

        .cat-pill.active {
            background: #ff5a00;
            color: #fff;
            border-color: #ff5a00;
        }

        /* Products Grid */
        .products-scroll {
            flex: 1;
            overflow-y: auto;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
            padding-bottom: 10px;
        }

        .product-tile {
            background: #fff;
            border-radius: 14px;
            border: 2px solid #f0f0f0;
            cursor: pointer;
            transition: all .15s;
            overflow: hidden;
        }

        .product-tile:hover {
            border-color: #ff5a00;
            transform: translateY(-2px);
            box-shadow: 0 4px 16px rgba(255, 90, 0, .15);
        }

        .product-tile:active {
            transform: scale(.97);
        }

        .product-tile img {
            width: 100%;
            height: 110px;
            object-fit: cover;
        }

        .product-tile-body {
            padding: 8px 10px 10px;
        }

        .product-tile-name {
            font-size: 13px;
            font-weight: 700;
            color: #111;
            line-height: 1.3;
            margin-bottom: 4px;
        }

        .product-tile-price {
            font-size: 14px;
            font-weight: 800;
            color: #ff5a00;
        }

        .product-tile-badge {
            font-size: 10px;
            background: #fff3ee;
            color: #ff5a00;
            padding: 2px 7px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 4px;
        }

        /* Right Panel */
        .pos-panel-header {
            padding: 16px 20px 12px;
            border-bottom: 1px solid #f0f0f0;
            flex-shrink: 0;
        }

        .pos-panel-header h5 {
            font-size: 16px;
            font-weight: 800;
            margin: 0 0 10px;
        }

        .pos-type-btns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 6px;
            margin-bottom: 10px;
        }

        .pos-type-btn {
            padding: 10px;
            border-radius: 10px;
            border: 2px solid #e5e7eb;
            background: #fff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-align: center;
            transition: all .15s;
        }

        .pos-type-btn.active {
            background: #111;
            color: #fff;
            border-color: #111;
        }

        .pos-type-btn i {
            display: block;
            font-size: 18px;
            margin-bottom: 2px;
        }

        .pos-field {
            width: 100%;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 9px 12px;
            font-size: 13px;
            outline: none;
            margin-bottom: 8px;
        }

        .pos-field:focus {
            border-color: #ff5a00;
        }

        /* Order Items */
        .pos-order-items {
            flex: 1;
            overflow-y: auto;
            padding: 12px 16px;
        }

        .order-item-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 8px;
        }

        .order-item-img {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            object-fit: cover;
            flex-shrink: 0;
        }

        .order-item-info {
            flex: 1;
            min-width: 0;
        }

        .order-item-name {
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .order-item-opts {
            font-size: 11px;
            color: #888;
            line-height: 1.4;
        }

        .order-item-price {
            font-size: 13px;
            font-weight: 800;
            color: #ff5a00;
        }

        .qty-ctrl {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: 1.5px solid #e5e7eb;
            background: #fff;
            font-size: 16px;
            line-height: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        .qty-btn:hover {
            background: #ff5a00;
            color: #fff;
            border-color: #ff5a00;
        }

        .qty-val {
            font-size: 14px;
            font-weight: 800;
            min-width: 20px;
            text-align: center;
        }

        .remove-btn {
            width: 28px;
            height: 28px;
            border-radius: 8px;
            border: none;
            background: #fee2e2;
            color: #dc2626;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
        }

        /* Footer */
        .pos-footer {
            padding: 14px 16px;
            border-top: 1px solid #f0f0f0;
            flex-shrink: 0;
        }

        .pos-summary-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #666;
            margin-bottom: 4px;
        }

        .pos-total-row {
            display: flex;
            justify-content: space-between;
            font-size: 20px;
            font-weight: 800;
            color: #111;
            margin: 8px 0 12px;
        }

        .btn-place {
            width: 100%;
            height: 52px;
            background: #ff5a00;
            color: #fff;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all .15s;
        }

        .btn-place:hover {
            background: #e04e00;
        }

        .btn-place:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-clear {
            background: none;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 6px 14px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-clear:hover {
            border-color: #dc2626;
            color: #dc2626;
        }

        /* Empty state */
        .pos-empty {
            text-align: center;
            color: #bbb;
            padding: 40px 20px;
        }

        .pos-empty i {
            font-size: 48px;
            display: block;
            margin-bottom: 10px;
        }

        /* Customer select2 */
        .customer-row {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-bottom: 8px;
        }

        .customer-row select {
            flex: 1;
        }

        .btn-new-customer {
            padding: 8px 12px;
            border-radius: 10px;
            background: #111;
            color: #fff;
            border: none;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }

        /* Modal */
        .pos-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .5);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .pos-modal {
            background: #fff;
            border-radius: 20px;
            width: 460px;
            max-width: 95vw;
            max-height: 90vh;
            overflow-y: auto;
            padding: 24px;
        }

        .pos-modal-title {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .option-group {
            margin-bottom: 16px;
        }

        .option-group-title {
            font-size: 13px;
            font-weight: 700;
            color: #555;
            margin-bottom: 8px;
            display: flex;
            justify-content: space-between;
        }

        .option-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border: 2px solid #f0f0f0;
            border-radius: 10px;
            margin-bottom: 6px;
            cursor: pointer;
            transition: all .15s;
        }

        .option-item:hover {
            border-color: #ff5a00;
        }

        .option-item.selected {
            border-color: #ff5a00;
            background: #fff8f5;
        }

        .option-item-name {
            flex: 1;
            font-size: 14px;
            font-weight: 600;
        }

        .option-item-price {
            font-size: 13px;
            color: #ff5a00;
            font-weight: 700;
        }

        .attr-btns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            margin-bottom: 16px;
        }

        .attr-btn {
            padding: 12px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            text-align: center;
            cursor: pointer;
            font-weight: 700;
            font-size: 13px;
            transition: all .15s;
        }

        .attr-btn.selected {
            border-color: #ff5a00;
            background: #fff8f5;
            color: #ff5a00;
        }

        .qty-row {
            display: flex;
            align-items: center;
            gap: 12px;
            justify-content: center;
            margin: 16px 0;
        }

        .qty-row .qty-btn {
            width: 38px;
            height: 38px;
            font-size: 20px;
        }

        .qty-row .qty-val {
            font-size: 22px;
            font-weight: 800;
            min-width: 40px;
        }

        .btn-add-to-order {
            width: 100%;
            height: 48px;
            background: #ff5a00;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            margin-top: 8px;
        }

        /* Quick Add Customer Modal */
        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
    </style>

    <div class="pos-wrapper">

        <div class="pos-left">
            <input type="text" id="posSearch" placeholder="🔍  Search products..."
                style="width:100%;height:46px;border-radius:12px;border:1.5px solid #e5e7eb;padding:0 16px;font-size:15px;outline:none;">

            <div class="cat-pills" id="catPills" style="display:flex;gap:8px;flex-wrap:wrap;">
                <div class="cat-pill active" data-cat="all">All</div>
                @foreach ($categories as $cat)
                    <div class="cat-pill" data-cat="{{ $cat->id }}">{{ $cat->name }}</div>
                @endforeach
            </div>

            <div class="products-scroll">
                <div class="products-grid" id="productsGrid">
                    @foreach ($categories as $category)
                        @foreach ($category->products as $product)
                            <div class="product-tile" data-cat="{{ $category->id }}"
                                data-name="{{ strtolower($product->title) }}" data-id="{{ $product->id }}"
                                data-has-options="{{ $product->options()->exists() ? 1 : 0 }}"
                                onclick="handleProductClick(this)">
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
                    <i class="ri-search-line"></i>
                    No products found
                </div>
            </div>
        </div>

        <div class="pos-right">
            <div class="pos-panel-header">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5>🧾 Current Order</h5>
                    <div class="d-flex gap-2 align-items-center">
                        <span class="badge bg-secondary" id="itemCountBadge">0 items</span>
                        <button class="btn-clear" onclick="clearOrder()">Clear</button>
                    </div>
                </div>

                <div class="pos-type-btns">
                    <div class="pos-type-btn active" data-type="collection" onclick="setDeliveryType('collection')">
                        <i class="ri-walk-line"></i> Collection
                    </div>
                    <div class="pos-type-btn" data-type="delivery" onclick="setDeliveryType('delivery')">
                        <i class="ri-e-bike-2-line"></i> Delivery
                    </div>
                </div>

                <select class="pos-field" id="posTimeSlot">
                    <option value="">⏰ Select Time Slot...</option>
                </select>

                <div class="customer-row">
                    <select class="pos-field select2" id="posCustomer" style="margin-bottom:0;">
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }}" data-name="{{ $client->name }}"
                                data-phone="{{ $client->phone }}" data-email="{{ $client->email }}">
                                {{ $client->name }} — {{ $client->phone }}
                            </option>
                        @endforeach
                    </select>
                    <button class="btn-new-customer" onclick="openNewCustomerModal()">+ New</button>
                </div>

                <textarea class="pos-field" id="posNotes" rows="2" placeholder="Order notes..." style="resize:none;"></textarea>
            </div>

            <div class="pos-order-items" id="orderItemsList">
                <div class="pos-empty" id="emptyMsg">
                    <i class="ri-shopping-basket-line"></i>
                    Tap a product to add
                </div>
            </div>

            <div class="pos-footer">
                <div class="pos-summary-row"><span>Subtotal</span><span id="posSubtotal">£0.00</span></div>
                <div class="pos-total-row"><span>Total</span><span id="posTotal">£0.00</span></div>
                <div
                    style="background:#f0fdf4;border-radius:10px;padding:8px 12px;font-size:13px;font-weight:700;color:#166534;margin-bottom:10px;">
                    💵 Cash on Delivery
                </div>
                <button class="btn-place" id="placeOrderBtn" onclick="placePosOrder()" disabled>
                    ✅ Place Order
                </button>
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
            <input class="pos-field" id="ncPassword" type="password" value="Password123!"
                placeholder="Default: Password123!">
            <div style="display:flex;gap:10px;margin-top:8px;">
                <button onclick="closeNewCustomerModal()"
                    style="flex:1;padding:12px;border-radius:10px;border:1.5px solid #e5e7eb;background:#fff;font-weight:700;cursor:pointer;">Cancel</button>
                <button onclick="saveNewCustomer()"
                    style="flex:1;padding:12px;border-radius:10px;border:none;background:#ff5a00;color:#fff;font-weight:800;cursor:pointer;">Save
                    Customer</button>
            </div>
        </div>
    </div>

    <div id="successModal" style="display:none;" class="pos-modal-overlay">
        <div class="pos-modal" style="text-align:center;">
            <div style="font-size:56px;margin-bottom:8px;">✅</div>
            <div style="font-size:20px;font-weight:800;margin-bottom:4px;">Order Placed!</div>
            <div style="font-size:13px;color:#888;margin-bottom:12px;">Order Number</div>
            <div style="font-size:28px;font-weight:900;color:#ff5a00;margin-bottom:20px;" id="successOrderNum">—</div>
            <button onclick="resetAfterSuccess()"
                style="width:100%;height:48px;background:#111;color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:800;cursor:pointer;">🛒
                New Order</button>
        </div>
    </div>

@endsection

@section('script')
    <script>
        $(function() {

            // ── Time Slots ──
            const SHOP_HOURS = {
                Monday: {
                    open: '16:30',
                    close: '23:30'
                },
                Tuesday: {
                    open: '16:30',
                    close: '23:30'
                },
                Wednesday: {
                    open: '16:30',
                    close: '23:30'
                },
                Thursday: {
                    open: '16:30',
                    close: '23:30'
                },
                Friday: {
                    open: '16:30',
                    close: '23:30'
                },
                Saturday: {
                    open: '16:30',
                    close: '23:30'
                },
                Sunday: {
                    open: '16:30',
                    close: '22:00'
                },
            };

            function generateTimeSlots() {
                const now = new Date();
                const dayName = now.toLocaleDateString('en-GB', {
                    weekday: 'long'
                });
                const hours = SHOP_HOURS[dayName];
                const [openH, openM] = hours.open.split(':').map(Number);
                const [closeH, closeM] = hours.close.split(':').map(Number);

                let cursor = new Date(now.getFullYear(), now.getMonth(), now.getDate(), openH, openM);
                const close = new Date(now.getFullYear(), now.getMonth(), now.getDate(), closeH, closeM);

                if (now > cursor) {
                    const rounded = Math.ceil(now.getMinutes() / 20) * 20;
                    if (rounded >= 60) {
                        cursor = new Date(now.getFullYear(), now.getMonth(), now.getDate(), now.getHours() + 1, 0);
                    } else {
                        cursor = new Date(now.getFullYear(), now.getMonth(), now.getDate(), now.getHours(),
                        rounded);
                    }
                }

                const slots = [];
                const fmt = d => d.toLocaleTimeString('en-GB', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });

                while (cursor < close) {
                    const end = new Date(cursor.getTime() + 20 * 60000);
                    if (end > close) break;
                    slots.push({
                        value: fmt(cursor) + '-' + fmt(end),
                        label: fmt(cursor) + ' – ' + fmt(end)
                    });
                    cursor = end;
                }
                return slots;
            }

            function populateSlots() {
                const slots = generateTimeSlots();
                let html = '<option value="">⏰ Select Time Slot...</option>';
                slots.forEach(s => {
                    html += `<option value="${s.value}">${s.label}</option>`;
                });
                $('#posTimeSlot').html(html);
            }
            populateSlots();

            // ── Category Filter ──
            $(document).on('click', '.cat-pill', function() {
                $('.cat-pill').removeClass('active');
                $(this).addClass('active');
                filterProducts();
            });

            $('#posSearch').on('input', filterProducts);

            function filterProducts() {
                const q = $('#posSearch').val().toLowerCase().trim();
                const cat = $('.cat-pill.active').data('cat');
                let visible = 0;

                $('.product-tile').each(function() {
                    const matchCat = cat === 'all' || $(this).data('cat') == cat;
                    const matchQ = !q || $(this).data('name').includes(q);
                    $(this).toggle(matchCat && matchQ);
                    if (matchCat && matchQ) visible++;
                });
                $('#noResults').toggle(visible === 0);
            }

            // ── Delivery Type ──
            let deliveryType = 'collection';
            window.setDeliveryType = function(type) {
                deliveryType = type;
                $('.pos-type-btn').removeClass('active');
                $(`.pos-type-btn[data-type="${type}"]`).addClass('active');
            };

            // ── Cart ──
            let cart = [];

            window.handleProductClick = function(el) {
                const id = $(el).data('id');
                const hasOptions = $(el).data('has-options') == 1;

                if (!hasOptions) {
                    $.get('/admin/pos/product', {
                        id
                    }, function(product) {
                        addToCartSimple(product, 1);
                    });
                } else {
                    $.get('/admin/pos/product', {
                        id
                    }, function(product) {
                        openProductModal(product);
                    });
                }
            };

            function addToCartSimple(product, qty) {
                const existing = cart.find(i => i.productId === product.id && i.type === 'direct' && !i.attribute);
                if (existing) {
                    existing.qty += qty;
                } else {
                    cart.push({
                        productId: product.id,
                        title: product.title,
                        price: product.price,
                        image: product.image,
                        skuRef: product.sku_ref,
                        qty: qty,
                        type: 'direct',
                        options: {},
                        attribute: false,
                        attributePrice: 0
                    });
                }
                renderOrder();
            }

            function openProductModal(product) {
                const basePrice = parseFloat(product.price);
                const attrPrice = parseFloat(product.attribute_price) || 0;

                window._modalBasePrice = basePrice;
                window._modalAttrPrice = attrPrice;

                let html = `<div class="pos-modal-title">${product.title}</div>`;
                html += `<div style="text-align:center;margin-bottom:16px;">
            <img src="${product.image}" style="width:100%;height:180px;object-fit:cover;border-radius:12px;">
            <div style="font-size:22px;font-weight:900;color:#ff5a00;margin-top:8px;">£<span id="modalPrice">${basePrice.toFixed(2)}</span></div>
        </div>`;

                if (product.has_attribute) {
                    html += `<div style="font-size:13px;font-weight:700;color:#555;margin-bottom:8px;">Choose Option *</div>
            <div class="attr-btns">
                <div class="attr-btn selected" data-attr="standalone" onclick="selectAttr(this, 'standalone', ${basePrice}, ${attrPrice})">
                    On Its Own<br><span style="font-size:12px;color:#888;">£${basePrice.toFixed(2)}</span>
                </div>
                <div class="attr-btn" data-attr="with_options" onclick="selectAttr(this, 'with_options', ${basePrice}, ${attrPrice})">
                    With Options<br><span style="font-size:12px;color:#888;">£${(basePrice + attrPrice).toFixed(2)}</span>
                </div>
            </div>
            <div id="optionsContainer" style="display:none;">`;
                } else {
                    html += `<div id="optionsContainer">`;
                }

                product.options.forEach(group => {
                    html += `<div class="option-group" data-group="${group.name}" data-max="${group.max || 0}" data-required="${group.required}">
                <div class="option-group-title">
                    <span>${group.name}</span>
                    <span style="font-size:11px;">${group.required ? '⚠️ Required' : 'Optional'}${group.max ? ' · Max ' + group.max : ''}</span>
                </div>`;
                    group.items.forEach(item => {
                        const itemPrice = parseFloat(item.price) || 0;
                        const inputType = group.type === 'single' ? 'radio' : 'checkbox';
                        html += `<div class="option-item" onclick="toggleOption(this, '${inputType}', '${group.name}', '${group.max}')">
                    <input type="${inputType}" name="opt_${group.name}" style="display:none;"
                        data-title="${item.title}" data-price="${itemPrice}" data-hubrise="${item.hubrise_option_ref}">
                    <span class="option-item-name">${item.title}</span>
                    ${itemPrice > 0 ? `<span class="option-item-price">+£${itemPrice.toFixed(2)}</span>` : ''}
                </div>`;
                    });
                    html += `</div>`;
                });

                html += `</div>`;

                html += `<div class="qty-row">
            <button class="qty-btn" onclick="modalQtyChange(-1)">−</button>
            <span class="qty-val" id="modalQty">1</span>
            <button class="qty-btn" onclick="modalQtyChange(1)">+</button>
        </div>`;

                const productJson = JSON.stringify(product).replace(/"/g, '&quot;');
                html +=
                    `<button class="btn-add-to-order" onclick="addFromModal(JSON.parse(this.dataset.product))" data-product="${productJson}">Add to Order</button>`;
                html +=
                    `<button onclick="closeProductModal()" style="width:100%;margin-top:8px;padding:12px;border-radius:12px;border:1.5px solid #e5e7eb;background:#fff;font-weight:700;cursor:pointer;">Cancel</button>`;

                $('#productModalInner').html(html);
                $('#productModal').show();
            }

            window.selectAttr = function(el, type, basePrice, attrPrice) {
                $('.attr-btn').removeClass('selected');
                $(el).addClass('selected');
                if (type === 'with_options') {
                    $('#optionsContainer').show();
                } else {
                    $('#optionsContainer').hide();
                    $('#optionsContainer .option-item').removeClass('selected').find('input').prop('checked',
                        false);
                }
                recalcModalPrice(basePrice, attrPrice);
            };

            window.toggleOption = function(el, inputType, groupName, max) {
                const input = $(el).find('input')[0];
                const group = $(el).closest('.option-group');

                if (inputType === 'radio') {
                    group.find('.option-item').removeClass('selected');
                    $(el).addClass('selected');
                    group.find('input').prop('checked', false);
                    input.checked = true;
                } else {
                    const maxVal = parseInt(max) || 0;
                    const checkedCount = group.find('input:checked').length;
                    if (!input.checked && maxVal && checkedCount >= maxVal) {
                        showError('Max ' + maxVal + ' selections allowed');
                        return;
                    }
                    $(el).toggleClass('selected');
                    input.checked = !input.checked;
                }

                const basePrice = parseFloat($('#productModal .attr-btn.selected').length ?
                    parseFloat($('#productModal .attr-btn[data-attr="with_options"]').length ?
                        0 : 0) : 0);
                updateModalPrice();
            };

            function updateModalPrice() {
                let extra = 0;
                $('#productModal input:checked').each(function() {
                    extra += parseFloat($(this).data('price')) || 0;
                });
                const attrSelected = $('#productModal .attr-btn.selected').data('attr');
                const base = window._modalBasePrice || 0;
                const attr = (attrSelected === 'with_options') ? (window._modalAttrPrice || 0) : 0;
                $('#modalPrice').text((base + attr + extra).toFixed(2));
            }

            window.recalcModalPrice = function(base, attr) {
                window._modalBasePrice = base;
                window._modalAttrPrice = attr;
                updateModalPrice();
            };

            let modalQty = 1;
            window.modalQtyChange = function(delta) {
                modalQty = Math.max(1, modalQty + delta);
                $('#modalQty').text(modalQty);
            };

            window.addFromModal = function(product) {
                const basePrice = parseFloat(product.price);
                const attrPriceRaw = parseFloat(product.attribute_price) || 0;
                const hasAttribute = $('#productModal .attr-btn').length > 0;
                const attrType = $('#productModal .attr-btn.selected').data('attr') || 'standalone';

                let valid = true;
                if (!hasAttribute || attrType === 'with_options') {
                    $('#optionsContainer .option-group').each(function() {
                        const required = $(this).data('required');
                        const hasSelection = $(this).find('input:checked').length > 0;
                        if (required && !hasSelection) {
                            showError('Please select: ' + $(this).data('group'));
                            valid = false;
                            return false;
                        }
                    });
                }
                if (!valid) return;

                let options = {};
                let extraPrice = 0;
                const attributePrice = (hasAttribute && attrType === 'with_options') ? attrPriceRaw : 0;

                if (!hasAttribute || attrType === 'with_options') {
                    $('#optionsContainer input:checked').each(function() {
                        const name = $(this).attr('name').replace('opt_', '');
                        const price = parseFloat($(this).data('price')) || 0;
                        extraPrice += price;
                        if (!options[name]) options[name] = [];
                        options[name].push({
                            title: $(this).data('title'),
                            price: price,
                            hubriseOptionRef: $(this).data('hubrise')
                        });
                    });
                }

                const finalPrice = basePrice + extraPrice + attributePrice;
                const qty = modalQty;

                cart.push({
                    productId: product.id,
                    title: product.title,
                    price: finalPrice,
                    image: product.image,
                    skuRef: product.sku_ref,
                    qty: qty,
                    type: Object.keys(options).length ? 'custom' : 'direct',
                    options: options,
                    attribute: hasAttribute && attrType === 'with_options',
                    attributePrice: attributePrice
                });

                modalQty = 1;
                closeProductModal();
                renderOrder();
            };

            window.closeProductModal = function() {
                $('#productModal').hide();
                $('#productModalInner').html('');
                modalQty = 1;
            };

            function renderOrder() {
                if (!cart.length) {
                    $('#orderItemsList').html(
                        '<div class="pos-empty" id="emptyMsg"><i class="ri-shopping-basket-line"></i>Tap a product to add</div>'
                        );
                    updateTotals();
                    return;
                }

                let html = '';
                cart.forEach((item, idx) => {
                    let optsHtml = '';
                    if (item.options && Object.keys(item.options).length) {
                        optsHtml = '<div class="order-item-opts">';
                        Object.values(item.options).forEach(arr => {
                            arr.forEach(o => {
                                optsHtml += o.title + (o.price > 0 ?
                                    ` (+£${o.price.toFixed(2)})` : '') + '<br>';
                            });
                        });
                        optsHtml += '</div>';
                    }

                    html += `<div class="order-item-row">
                <img src="${item.image}" class="order-item-img" alt="">
                <div class="order-item-info">
                    <div class="order-item-name">${item.title}</div>
                    ${optsHtml}
                    <div class="order-item-price">£${(item.price * item.qty).toFixed(2)}</div>
                </div>
                <div class="qty-ctrl">
                    <button class="qty-btn" onclick="changeQty(${idx}, -1)">−</button>
                    <span class="qty-val">${item.qty}</span>
                    <button class="qty-btn" onclick="changeQty(${idx}, 1)">+</button>
                </div>
                <button class="remove-btn" onclick="removeItem(${idx})"><i class="ri-delete-bin-line"></i></button>
            </div>`;
                });

                $('#orderItemsList').html(html);
                updateTotals();
            }

            window.changeQty = function(idx, delta) {
                cart[idx].qty += delta;
                if (cart[idx].qty <= 0) cart.splice(idx, 1);
                renderOrder();
            };

            window.removeItem = function(idx) {
                cart.splice(idx, 1);
                renderOrder();
            };

            function updateTotals() {
                const subtotal = cart.reduce((s, i) => s + i.price * i.qty, 0);
                const count = cart.reduce((s, i) => s + i.qty, 0);
                $('#posSubtotal').text('£' + subtotal.toFixed(2));
                $('#posTotal').text('£' + subtotal.toFixed(2));
                $('#itemCountBadge').text(count + (count === 1 ? ' item' : ' items'));
                $('#placeOrderBtn').prop('disabled', cart.length === 0);
            }

            window.clearOrder = function() {
                cart = [];
                $('#posCustomer').val('');
                $('#posNotes').val('');
                $('#posTimeSlot').val('');
                setDeliveryType('collection');
                renderOrder();
            };

            // ── Place Order ──
            window.placePosOrder = function() {
                if (!cart.length) {
                    showError('Add at least one item');
                    return;
                }
                const timeSlot = $('#posTimeSlot').val();
                if (!timeSlot) {
                    showError('Please select a time slot');
                    return;
                }

                const selectedCustomer = $('#posCustomer option:selected');
                const customerId = $('#posCustomer').val();
                let firstName = 'Walk-in',
                    lastName = 'Customer',
                    email = 'pos@internal.local',
                    phone = '00000000000';

                if (customerId) {
                    const fullName = selectedCustomer.data('name') || 'Walk-in Customer';
                    firstName = fullName.split(' ')[0] || fullName;
                    lastName = fullName.split(' ').slice(1).join(' ') || 'POS';
                    email = selectedCustomer.data('email') || 'pos@internal.local';
                    phone = selectedCustomer.data('phone') || '00000000000';
                }

                const orderData = {
                    customer: {
                        firstName,
                        lastName,
                        email,
                        phone
                    },
                    delivery: {
                        type: deliveryType,
                        time: timeSlot,
                        postcode: ''
                    },
                    address: '',
                    address2: '',
                    city: '',
                    cart: cart.map(i => ({
                        productId: i.productId,
                        quantity: i.qty,
                        type: i.type,
                        options: i.options || null,
                        attribute: i.attribute || false,
                        attributePrice: i.attributePrice || 0
                    })),
                    promoCode: null,
                    pointsToUse: 0,
                    paymentMethod: 'cash',
                    notes: $('#posNotes').val().trim(),
                    pos: true
                };

                $('#placeOrderBtn').prop('disabled', true).text('Placing...');

                $.ajax({
                    url: '/place-order',
                    type: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify(orderData),
                    success: function(res) {
                        if (res.orderNumber) {
                            $('#successOrderNum').text(res.orderNumber);
                            $('#successModal').show();
                        } else if (res.redirectUrl) {
                            window.location.href = res.redirectUrl;
                        }
                        $('#placeOrderBtn').prop('disabled', false).text('✅ Place Order');
                    },
                    error: function(xhr) {
                        $('#placeOrderBtn').prop('disabled', false).text('✅ Place Order');
                        showError(xhr.responseJSON?.message || 'Error placing order');
                    }
                });
            };

            window.resetAfterSuccess = function() {
                $('#successModal').hide();
                clearOrder();
                populateSlots();
            };

            // ── New Customer ──
            window.openNewCustomerModal = function() {
                $('#newCustomerModal').show();
            };
            window.closeNewCustomerModal = function() {
                $('#newCustomerModal').hide();
            };

            window.saveNewCustomer = function() {
                const data = {
                    first_name: $('#ncFirstName').val().trim(),
                    last_name: $('#ncLastName').val().trim(),
                    email: $('#ncEmail').val().trim(),
                    phone: $('#ncPhone').val().trim(),
                    password: $('#ncPassword').val() || 'Password123!',
                };

                if (!data.first_name || !data.last_name || !data.email || !data.phone) {
                    showError('Please fill all required fields');
                    return;
                }

                $.ajax({
                    url: '/admin/pos/quick-customer',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: function(res) {
                        const opt = `<option value="${res.id}" data-name="${res.name}" data-phone="${res.phone}" data-email="${res.email}" selected>
                    ${res.name} — ${res.phone}
                </option>`;
                        $('#posCustomer').append(opt).val(res.id);
                        closeNewCustomerModal();
                        $('#ncFirstName,#ncLastName,#ncEmail,#ncPhone').val('');
                        showSuccess('Customer created!');
                    },
                    error: function(xhr) {
                        showError(xhr.responseJSON?.message || 'Error creating customer');
                    }
                });
            };

            // Close modals on overlay click
            $('#productModal').on('click', function(e) {
                if (e.target === this) closeProductModal();
            });
            $('#newCustomerModal').on('click', function(e) {
                if (e.target === this) closeNewCustomerModal();
            });
        });
    </script>
@endsection
