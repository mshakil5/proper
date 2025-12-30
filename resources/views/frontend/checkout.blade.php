@extends('frontend.master')

@section('content')
<div class="checkout-wrapper">
    <div class="container py-4">
        <div class="row g-4">
            <!-- Left Column: Customer & Delivery Details -->
            <div class="col-lg-8">
                <!-- Customer Details Card -->
                <div class="checkout-card">
                    <h5 class="checkout-title">Customer Details</h5>
                    <form id="customerForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="firstName" placeholder="John" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name <span class="required">*</span></label>
                                <input type="text" class="form-control" id="lastName" placeholder="Doe" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email <span class="required">*</span></label>
                                <input type="email" class="form-control" id="email" placeholder="john@example.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Phone <span class="required">*</span></label>
                                <input type="tel" class="form-control" id="phone" placeholder="+44 123 456 7890" required>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Delivery Address Card -->
                <div class="checkout-card">
                    <h5 class="checkout-title">Delivery Address</h5>
                    <form id="addressForm">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Address <span class="required">*</span></label>
                                <input type="text" class="form-control" id="address" placeholder="123 Main Street" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City <span class="required">*</span></label>
                                <input type="text" class="form-control" id="city" placeholder="London" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Postal Code <span class="required">*</span></label>
                                <input type="text" class="form-control" id="postalCode" placeholder="SW1A 1AA" required>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Delivery & Collection Details Card -->
                <div class="checkout-card">
                    <h5 class="checkout-title">Delivery & Collection Details</h5>
                    <div id="deliveryDetailsDisplay" style="background: #f8f9fa; padding: 16px; border-radius: 8px;">
                        <div class="delivery-detail-row">
                            <span class="detail-label">Type:</span>
                            <span class="detail-value" id="deliveryTypeDisplay">-</span>
                        </div>
                        <div class="delivery-detail-row">
                            <span class="detail-label">Time:</span>
                            <span class="detail-value" id="deliveryTimeDisplay">-</span>
                        </div>
                        <div class="delivery-detail-row" id="postcodeRow" style="display: none;">
                            <span class="detail-label">Postcode:</span>
                            <span class="detail-value" id="deliveryPostcodeDisplay">-</span>
                        </div>
                        <div class="delivery-detail-row">
                            <span class="detail-label">Delivery Charge:</span>
                            <span class="detail-value" id="deliveryChargeDisplay">£0.00</span>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Card -->
                <div class="checkout-card">
                    <h5 class="checkout-title">Payment Method</h5>
                    <div class="payment-method">
                        <div class="payment-badge">
                            <i class="fas fa-money-bill-wave"></i>
                            <div class="payment-info">
                                <strong>Cash on Delivery</strong>
                                <small>Pay when your order arrives</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="col-lg-4">
                <div class="order-summary-card">
                    <h5 class="checkout-title">Order Summary</h5>

                    <!-- Cart Items -->
                    <div class="checkout-cart-body" id="summaryItemsContainer">
                        <!-- Items loaded from localStorage -->
                    </div>

                    <div class="summary-divider"></div>

                    <!-- Pricing -->
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span id="summarySubtotal">£0.00</span>
                    </div>
                    <div class="summary-row">
                        <span>Delivery Charge</span>
                        <span id="summaryDeliveryCharge">£0.00</span>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-row total">
                        <span>Total</span>
                        <span id="summaryTotal">£0.00</span>
                    </div>

                    <button id="confirmOrderBtn" class="btn-place-order">Confirm Order</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
$(function() {
    let checkoutData = JSON.parse(localStorage.getItem('checkoutData')) || null;

    if (!checkoutData) {
        alert('No cart data found. Redirecting to cart...');
        window.location.href = '/';
        return;
    }

    function displayDeliveryDetails() {
        let delivery = checkoutData.delivery;

        if (delivery.type === 'delivery') {
            $('#deliveryTypeDisplay').text('Home Delivery');
            $('#postcodeRow').show();
            $('#deliveryPostcodeDisplay').text(delivery.postcode);
        } else {
            $('#deliveryTypeDisplay').text('Collection');
            $('#postcodeRow').hide();
        }

        $('#deliveryTimeDisplay').text(delivery.time);
        $('#deliveryChargeDisplay').text('£' + delivery.charge.toFixed(2));
    }

    function displaySummaryItems() {
        let itemsHTML = '';

        checkoutData.cart.forEach(item => {
            let optionsHTML = '';

            if (item.type === 'custom' && item.options) {
                optionsHTML = '<ul class="cart-item-options">';
                Object.values(item.options).forEach(optArr => {
                    optArr.forEach(opt => {
                        optionsHTML += `<li>${escapeHtml(opt.title)}</li>`;
                    });
                });
                optionsHTML += '</ul>';
            }

            itemsHTML += `
                <div class="cart-item-row">
                    <div style="display: grid; grid-template-columns: auto 1fr; gap: 10px;">
                        <div>
                            <img src="${escapeHtml(item.image)}" class="cart-item-img" alt="${escapeHtml(item.title)}">
                        </div>
                        <div>
                            <p class="cart-product-name">${escapeHtml(item.title)}</p>
                            ${optionsHTML}
                            <div class="cart-item-controls">
                                <span class="cart-product-price">£${(item.price).toFixed(2)}</span>
                                <span style="font-size: 12px; font-weight: 600; color: #666;">x${item.quantity}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        $('#summaryItemsContainer').html(itemsHTML);
    }

    function displayTotals() {
        $('#summarySubtotal').text('£' + checkoutData.subtotal.toFixed(2));
        $('#summaryDeliveryCharge').text('£' + checkoutData.deliveryCharge.toFixed(2));
        $('#summaryTotal').text('£' + checkoutData.total.toFixed(2));
    }

    function escapeHtml(text) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, m => map[m]);
    }

    $('#confirmOrderBtn').on('click', function() {
        let firstName = $('#firstName').val().trim();
        let lastName = $('#lastName').val().trim();
        let email = $('#email').val().trim();
        let phone = $('#phone').val().trim();
        let address = $('#address').val().trim();
        let city = $('#city').val().trim();
        let postalCode = $('#postalCode').val().trim();

        if (!firstName || !lastName || !email || !phone || !address || !city || !postalCode) {
            showError('Please fill in all required fields');
            return;
        }

        // Build HubRise order items from cart
        let hubRiseItems = [];
        let totalPrice = 0;

        checkoutData.cart.forEach(item => {
            let itemPrice = Number(item.price);
            let itemQuantity = Number(item.quantity);
            
            // Calculate options price for this item
            let optionsPrice = 0;
            if (item.type === 'custom' && item.options && Object.keys(item.options).length > 0) {
                Object.entries(item.options).forEach(([optionName, optionValues]) => {
                    optionValues.forEach(opt => {
                        optionsPrice += Number(opt.price || 0);
                    });
                });
            }

            // Final price per item = base price (already includes options in your cart)
            // So just use item.price as is
            let itemTotal = itemPrice * itemQuantity;
            totalPrice += itemTotal;

            let priceValue = itemPrice.toFixed(2);

            let hubRiseItem = {
                product_name: item.title,
                quantity: itemQuantity,
                price: priceValue + ' BDT'
            };

            // Add options separately - DO NOT include in price
            if (item.type === 'custom' && item.options && Object.keys(item.options).length > 0) {
                hubRiseItem.options = [];
                Object.entries(item.options).forEach(([optionName, optionValues]) => {
                    optionValues.forEach(opt => {
                        hubRiseItem.options.push({
                            option_list_name: optionName,
                            name: opt.title,
                            price: (opt.price || 0).toFixed(2) + ' BDT'
                        });
                    });
                });
            }

            hubRiseItems.push(hubRiseItem);
        });

        // Add delivery charge if applicable
        totalPrice += checkoutData.deliveryCharge;

        // Build HubRise order object matching documentation
        let hubRiseOrder = {
            status: 'new',
            channel: 'Website',
            service_type: checkoutData.delivery.type === 'delivery' ? 'delivery' : 'collection',
            items: hubRiseItems,
            payments: [
                {
                    amount: totalPrice.toFixed(2) + ' BDT'
                }
            ],
            customer: {
                first_name: firstName,
                last_name: lastName,
                email: email,
                phone_number: phone,
                address: address
            }
        };

        // Add delivery charge if applicable (as charges, not in items)
        if (checkoutData.delivery.type === 'delivery') {
            hubRiseOrder.charges = [
                {
                    name: 'Delivery',
                    amount: checkoutData.deliveryCharge.toFixed(2) + ' BDT'
                }
            ];
        }

        console.log('HubRise Order:', hubRiseOrder);
        console.log('Total Price:', totalPrice.toFixed(2) + ' BDT');

        // Send to backend
        $.ajax({
            url: '/place-order',
            type: 'POST',
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify({
                hubRiseOrder: hubRiseOrder,
                localOrder: {
                    customer: {
                        firstName: firstName,
                        lastName: lastName,
                        email: email,
                        phone: phone,
                        address: address,
                        city: city,
                        postalCode: postalCode
                    },
                    cart: checkoutData.cart,
                    delivery: checkoutData.delivery,
                    subtotal: checkoutData.subtotal,
                    deliveryCharge: checkoutData.deliveryCharge,
                    total: totalPrice
                }
            }),
            success: function(response) {
                console.log('Order placed successfully:', response);

                localStorage.removeItem('cart');
                localStorage.removeItem('cartSummary');
                localStorage.removeItem('deliveryOptions');
                localStorage.removeItem('checkoutData');

                showSuccess('Order placed successfully!');

                setTimeout(() => {
                    window.location.href = '/order-confirmation/' + response.orderId;
                }, 1500);
            },
            error: function(err) {
                console.error('Error placing order:', err);
                if (err.responseJSON && err.responseJSON.message) {
                    showError(err.responseJSON.message);
                } else {
                    showError('Error placing order. Please try again.');
                }
            }
        });
    });

    displayDeliveryDetails();
    displaySummaryItems();
    displayTotals();
});
</script>
@endsection