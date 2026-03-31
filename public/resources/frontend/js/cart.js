$(function () {

    const SHOP_HOURS = {
        Monday:    { open: '16:30', close: '23:30' },
        Tuesday:   { open: '16:30', close: '23:30' },
        Wednesday: { open: '16:30', close: '23:30' },
        Thursday:  { open: '16:30', close: '23:30' },
        Friday:    { open: '16:30', close: '23:30' },
        Saturday:  { open: '16:30', close: '23:30' },
        Sunday:    { open: '16:30', close: '22:00' },
    };

    let selectedDelivery = {
        type: '',
        postcode: '',
        charge: 0,
        time: '',
        isValid: false
    };

    localStorage.setItem('deliveryOptions', JSON.stringify(selectedDelivery));

    function loadDeliveryData() {
        let stored = localStorage.getItem('deliveryOptions');
        if (!stored) return;

        selectedDelivery = JSON.parse(stored);

        $('input[name="deliveryType"]').prop('checked', false);
        $('#deliveryMode').hide();
        $('#collectionMode').hide();

        if (selectedDelivery.type === 'collection') {
            $('input[name="deliveryType"][value="collection"]').prop('checked', true);
            $('#collectionMode').show();

            if (
                selectedDelivery.time &&
                $('#collectionMode .delivery-time-select option[value="' + selectedDelivery.time + '"]').length
            ) {
                $('#collectionMode .delivery-time-select').val(selectedDelivery.time);
            }
        } else if (selectedDelivery.type === 'delivery') {
            $('input[name="deliveryType"][value="delivery"]').prop('checked', true);
            $('#deliveryMode').show();

            if (selectedDelivery.postcode) {
                $('#deliveryPostcode').val(selectedDelivery.postcode);
            }

            if (
                selectedDelivery.time &&
                $('#deliveryMode .delivery-time-select option[value="' + selectedDelivery.time + '"]').length
            ) {
                $('#deliveryMode .delivery-time-select').val(selectedDelivery.time);
            }
        }
        saveDeliveryData();
    }

    function saveDeliveryData() {
        localStorage.setItem('deliveryOptions', JSON.stringify(selectedDelivery));
    }

    function sanitizeCart() {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart = cart.filter(item =>
            Number.isFinite(Number(item.price)) &&
            Number.isFinite(Number(item.quantity)) &&
            Number(item.quantity) > 0
        );
        cart = cart.map(item => ({
            ...item,
            price: Number(item.price),
            quantity: Number(item.quantity),
            title: String(item.title || '').trim(),
            image: String(item.image || '').trim(),
            productId: item.productId ? Number(item.productId) : null,
            options: item.options || {}
        }));
        localStorage.setItem('cart', JSON.stringify(cart));
        return cart;
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

    function createProductHash(productId, title, options) {
        let optionString = '';
        
        if (Object.keys(options).length > 0) {
            optionString = Object.keys(options)
                .sort()
                .map(key => {
                    let optTitles = options[key].map(o => o.title).sort().join('|');
                    return key + ':' + optTitles;
                })
                .join('||');
        }
        
        return productId + '::' + title + (optionString ? '::' + optionString : '');
    }

    function generateTimeSlots(startHour, startMinute) {
        let slots = [];
        
        const parts = new Intl.DateTimeFormat('en-GB', {
            timeZone: 'Europe/London',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        }).formatToParts(new Date());

        const year = +parts.find(p => p.type === 'year').value;
        const month = +parts.find(p => p.type === 'month').value - 1;
        const date = +parts.find(p => p.type === 'day').value;
        const hour = +parts.find(p => p.type === 'hour').value;
        const minute = +parts.find(p => p.type === 'minute').value;
        
        let ukTime = new Date(year, month, date, hour, minute, 0);
        let current = new Date(year, month, date, startHour, startMinute, 0);
        let targetDate = date;
        let targetMonth = month;
        let targetYear = year;

        let dayName = current.toLocaleDateString('en-GB', { weekday: 'long' });
        let closeTime = SHOP_HOURS[dayName].close;
        let [closeHour, closeMinute] = closeTime.split(':').map(Number);

        if (current < ukTime) {
            let mins = ukTime.getMinutes();
            let rounded = Math.ceil(mins / 20) * 20;
            if (rounded === 60) {
                current = new Date(year, month, date, ukTime.getHours() + 1, 0, 0);
            } else {
                current = new Date(year, month, date, ukTime.getHours(), rounded, 0);
            }
        }

        let currentTimeInMinutes = current.getHours() * 60 + current.getMinutes();
        let closeTimeInMinutes = closeHour * 60 + closeMinute;

        if (currentTimeInMinutes >= closeTimeInMinutes) {
            let nextDate = new Date(year, month, date + 1, startHour, startMinute, 0);
            current = nextDate;
            targetDate = nextDate.getDate();
            targetMonth = nextDate.getMonth();
            targetYear = nextDate.getFullYear();
            
            let nextDayName = nextDate.toLocaleDateString('en-GB', { weekday: 'long' });
            let nextCloseTime = SHOP_HOURS[nextDayName].close;
            [closeHour, closeMinute] = nextCloseTime.split(':').map(Number);
        }

        let endTime = new Date(targetYear, targetMonth, targetDate, closeHour, closeMinute, 0);

        while ((current.getHours() * 60 + current.getMinutes()) + 20 <= closeHour * 60 + closeMinute) {
            let dayName = current.toLocaleDateString('en-GB', { weekday: 'long' });
            let start = current.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true });
            let end = new Date(current);
            end.setMinutes(end.getMinutes() + 20);
            let endSlot = end.toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit', hour12: true });

            slots.push({
                value: start + '-' + endSlot,
                label: start + ' - ' + endSlot + ' (' + dayName + ')'
            });

            current.setMinutes(current.getMinutes() + 20);
        }

        return slots;
    }

    function populateTimeSlots(selector, startHour, startMinute) {
        let slots = generateTimeSlots(startHour, startMinute);
        
        let html = '<option value="">Select Time</option>';
        slots.forEach(slot => {
            html += `<option value="${slot.value}">${slot.label}</option>`;
        });
        
        $(selector).html(html);
    }

    function updateDeliveryStartTimes() {
        let deliverySlots = generateTimeSlots(16, 30);
        let collectionSlots = generateTimeSlots(16, 30);
        
        if (deliverySlots.length > 0) {
            $('#deliveryStartTime').text(deliverySlots[0].label.split(' - ')[0]);
        }
        
        if (collectionSlots.length > 0) {
            $('#collectionStartTime').text(collectionSlots[0].label.split(' - ')[0]);
        }
    }

    function updateTotalPrice() {
        let basePrice = Number($('#totalPrice').data('base-price')) || 0;
        let extraPrice = 0;
        let attributePrice = 0;

        let attributeSelect = $('input[name="attribute_select"]:checked').val();
        let hasAttribute = $('[name="attribute_select"]').length > 0;

        if (hasAttribute && attributeSelect === 'with_options') {
            attributePrice = Number($('[data-attribute-price]').data('attribute-price')) || 0;
        }

        if (hasAttribute && attributeSelect === 'with_options') {
            $('#optionsContainer').find('.option-input:checked').each(function () {
                extraPrice += Number($(this).data('price')) || 0;
            });
        } else if (!hasAttribute) {
            $('.option-input:checked').each(function () {
                extraPrice += Number($(this).data('price')) || 0;
            });
        }

        let qty = Number($('#quantity').val()) || 1;
        let total = (basePrice + extraPrice + attributePrice) * qty;
        if (Number.isFinite(total)) {
            $('#totalPrice').text('£' + total.toFixed(2));
        }
    }

    function updateCartUI() {
        let cart = sanitizeCart();
        let totalQty = cart.reduce((sum, item) => sum + item.quantity, 0);
        $('.cart-badge').text(totalQty);
        $('#itemCount').text(totalQty);

        let subtotal = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        let total = subtotal + selectedDelivery.charge;

        $('#cartSubtotal').text('£' + subtotal.toFixed(2));
        $('#cartDeliveryCharge').text('£' + selectedDelivery.charge.toFixed(2));
        $('#cartTotal').text('£' + total.toFixed(2));

        let cartSummary = {
            subtotal: subtotal,
            deliveryCharge: selectedDelivery.charge,
            total: total,
            itemCount: totalQty
        };
        localStorage.setItem('cartSummary', JSON.stringify(cartSummary));

        if (totalQty === 0) {
            $('.cart-checkout-btn').prop('disabled', true);
        } else {
            $('.cart-checkout-btn').prop('disabled', false);
        }
    }

    function addToCartDirectly(element) {
        let cart = sanitizeCart();
        let productId = element.data('id');

        let existingItem = cart.find(item => item.productId === productId && item.type === 'direct');
        if (existingItem) existingItem.quantity += 1;
        else {
            cart.push({
                productId: productId,
                id: productId,
                title: String(element.data('title') || '').trim(),
                image: String(element.data('image') || '').trim(),
                price: Number(element.data('price')) || 0,
                skuRef: String(element.data('sku-ref') || '').trim(),
                category: String(element.data('category') || '').trim(),
                quantity: 1,
                type: "direct"
            });
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
        showSuccess('Added to cart!');
    }

    function renderCart() {
        let cart = sanitizeCart();
        let cartBody = $('#cartBody');
        if (!cart.length) {
            cartBody.html('<div class="cart-empty"><i class="fas fa-shopping-bag"></i><p>Your cart is empty</p></div>');
            return;
        }

        let html = '';
        cart.forEach((item, index) => {
            let optionsHTML = '';
            let basePrice = 0;
            
            if (item.type === 'custom' && item.options) {
                optionsHTML = '<ul class="cart-item-options">';
                Object.values(item.options).forEach(optArr => {
                    optArr.forEach(opt => {
                        optionsHTML += `<li>${escapeHtml(opt.title)}${opt.price > 0 ? ' (+£' + Number(opt.price).toFixed(2) + ')' : ''}</li>`;
                    });
                });
                optionsHTML += '</ul>';
                
                let optionsPrice = 0;
                Object.values(item.options).forEach(optArr => {
                    optArr.forEach(opt => {
                        optionsPrice += Number(opt.price) || 0;
                    });
                });
                basePrice = Number(item.price) - optionsPrice - (item.attributePrice || 0);
            }

            html += `
            <div class="cart-item-row">
                <div style="display: grid; grid-template-columns: auto 1fr; gap: 10px;">
                    <div>
                        <img src="${escapeHtml(item.image)}" class="cart-item-img" alt="${escapeHtml(item.title)}">
                    </div>
                    <div>
                        <p class="cart-product-name">
                            ${escapeHtml(item.title)}
                            ${item.type === 'custom' && basePrice > 0 ? `<span>(£${basePrice.toFixed(2)})</span>` : ''}
                        </p>
                        ${optionsHTML}
                        <div class="cart-item-controls">
                            <span class="cart-product-price">£${Number(item.price).toFixed(2)}</span>
                            <div style="display: flex; gap: 6px; align-items: center;">
                                <div class="cart-qty-control">
                                    <button class="cart-qty-btn cart-qty-minus" data-index="${index}">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="cart-qty-display">${Number(item.quantity)}</span>
                                    <button class="cart-qty-btn cart-qty-plus" data-index="${index}">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <button class="cart-remove-btn" data-index="${index}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        });

        cartBody.html(html);
        updateCartUI();
    }

    function continueCheckout() {
        if (!selectedDelivery.type) {
            showError('Please select Delivery or Collection');
            return;
        }

        if (selectedDelivery.type === 'delivery') {
            if (!selectedDelivery.isValid) {
                showError('Please verify your postcode for delivery');
                return;
            }
            if (!selectedDelivery.time) {
                showError('Please select delivery time');
                return;
            }
        } else {
            if (!selectedDelivery.time) {
                showError('Please select collection time');
                return;
            }
        }

        saveDeliveryData();
        let cart = sanitizeCart();
        
        let checkoutData = {
            cart: cart,
            delivery: selectedDelivery,
            subtotal: cart.reduce((sum, item) => sum + item.price * item.quantity, 0),
            deliveryCharge: selectedDelivery.charge,
            total: cart.reduce((sum, item) => sum + item.price * item.quantity, 0) + selectedDelivery.charge,
            timestamp: new Date().toISOString()
        };

        localStorage.setItem('checkoutData', JSON.stringify(checkoutData));
        
        showSuccess('Proceeding to checkout...');
        
        setTimeout(() => {
            window.location.href = '/checkout';
        }, 1000);
    }

    function showAlertModal(title, message, backCallback, backButtonText = "Go Back", continueButtonText = "Continue") {
        const modalHtml = `
            <div class="cart-overlay open" id="alertOverlay"></div>
            <div class="alert-modal" id="alertModal">
                <div class="subscription-alert alert-warning" style="margin: 0; border-left: 4px solid #ff9800;">
                    <div class="alert-content">
                        <div class="alert-icon">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="alert-text">
                            <div class="alert-title">${title}</div>
                            <div class="alert-message">${message}</div>
                        </div>
                    </div>
                </div>
                <div class="alert-modal-actions">
                    <button class="btn-alert-action" id="goBack" style="background: #1a1a1a; flex: 1;">
                        <i class="fas fa-arrow-left"></i> ${backButtonText}
                    </button>
                    <button class="btn-alert-action" id="confirmContinue" style="background: linear-gradient(135deg, #ff8a00, #ff5a00); flex: 1;">
                        <i class="fas fa-check"></i> ${continueButtonText}
                    </button>
                </div>
            </div>
        `;

        const container = document.createElement('div');
        container.innerHTML = modalHtml;
        document.body.appendChild(container);

        const overlay = document.getElementById('alertOverlay');

        document.getElementById('confirmContinue').addEventListener('click', () => {
            container.remove();
            continueCheckout();
        });

        document.getElementById('goBack').addEventListener('click', () => {
            container.remove();
            if (backCallback) backCallback();
        });

        overlay.addEventListener('click', () => {
            container.remove();
        });
    }

    function showSubscriptionPromo() {
        if (!isAuthenticated) {
            let loginPromoHTML = `
                <div class="delivery-charge-promo login-promo" id="deliveryPromo">
                    <div class="promo-content">
                        <div class="promo-icon">
                            <i class="fas fa-lock"></i>
                        </div>
                        <div class="promo-text">
                            <strong>Sign In for Special Deals</strong>
                            <small>Get exclusive discounts on delivery charges</small>
                        </div>
                    </div>
                    <button class="btn-promo-link" id="cartLoginBtn">
                        Sign In <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            `;
            
            $('#deliveryPromo').remove();
            $('#cartDeliveryCharge').closest('.cart-summary-row').after(loginPromoHTML);

            $('#cartLoginBtn').on('click', function(e) {
                e.preventDefault();
                window.location.href = '/login';
            });
            return;
        }

        if (hasActiveSubscription) {
            return;
        }
        
        let promoHTML = `
            <div class="delivery-charge-promo" id="deliveryPromo">
                <div class="promo-content">
                    <div class="promo-icon">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="promo-text">
                        <strong>Why not Free Delivery?</strong>
                        <small>Just £5/month - Unlimited free delivery</small>
                    </div>
                </div>
                <button class="btn-promo-link" id="cartSubscribeBtn">
                    Subscribe <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        `;
        
        $('#deliveryPromo').remove();
        $('#cartDeliveryCharge').closest('.cart-summary-row').after(promoHTML);

        $('#cartSubscribeBtn').on('click', function(e) {
            e.preventDefault();
            showCartSubscriptionModal();
        });
    }

    function showCartSubscriptionModal() {
        let modalHTML = `
            <div id="cartSubscriptionPaymentModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title">Select Payment Method</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="payment-options">
                                <div class="payment-option" data-method="stripe">
                                    <input type="radio" name="cartSubscriptionPaymentMethod" id="cartSubscriptionPaymentStripe" value="stripe" class="form-check-input">
                                    <label for="cartSubscriptionPaymentStripe" class="payment-option-label">
                                        <i class="fab fa-stripe"></i>
                                        <div class="payment-option-text">
                                            <strong>Stripe</strong>
                                            <small>Secure card payment</small>
                                        </div>
                                    </label>
                                </div>

                                <div class="payment-option" data-method="paypal">
                                    <input type="radio" name="cartSubscriptionPaymentMethod" id="cartSubscriptionPaymentPaypal" value="paypal" class="form-check-input">
                                    <label for="cartSubscriptionPaymentPaypal" class="payment-option-label">
                                        <i class="fab fa-paypal"></i>
                                        <div class="payment-option-text">
                                            <strong>PayPal</strong>
                                            <small>Fast and secure checkout</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-gradient" id="confirmCartSubscriptionPaymentBtn">Confirm & Pay</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#cartSubscriptionPaymentModal').remove();
        $('body').append(modalHTML);

        $('#cartSubscriptionPaymentStripe').prop('checked', true);

        const modal = new bootstrap.Modal(document.getElementById('cartSubscriptionPaymentModal'));
        modal.show();

        $('#confirmCartSubscriptionPaymentBtn').off('click').on('click', function() {
            let selectedPaymentMethod = $('input[name="cartSubscriptionPaymentMethod"]:checked').val();
            
            if (!selectedPaymentMethod) {
                showError('Please select a payment method');
                return;
            }

            modal.hide();
            $('#loadingModal').css('display', 'flex');
            
            $.ajax({
                url: '/user/subscription/checkout',
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    amount: 5.00,
                    payment_method: selectedPaymentMethod
                },
                success: function(response) {
                    if (response.success) {
                        window.location.href = response.redirectUrl;
                    } else {
                        $('#loadingModal').css('display', 'none');
                        showError(response.message || 'Failed to process');
                    }
                },
                error: function(xhr) {
                    $('#loadingModal').css('display', 'none');
                    showError(xhr.responseJSON?.message || 'Error processing subscription');
                }
            });
        });

        document.getElementById('cartSubscriptionPaymentModal').addEventListener('hidden.bs.modal', function() {
            $('#cartSubscriptionPaymentModal').remove();
        });
    }

    $(document).on('click', '.open-product', function () {
        let hasOptions = $(this).data('has-options') == 1;

        if (!hasOptions) {
            addToCartDirectly($(this));
        } else {
            $.ajax({
                url: '/product',
                type: 'GET',
                data: { id: $(this).data('id') },
                success: function (res) {
                    $('#productModal .modal-body').html(res.html);
                    const modalEl = document.getElementById('productModal');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                    updateTotalPrice();
                    initLinkedOptionFilter();
                },
                error: function (err) {}
            });
        }
    });

    $(document).on('click', '.qty-plus', function () {
        let input = $('#quantity');
        let val = Number(input.val()) || 1;
        input.val(val + 1);
        updateTotalPrice();
    });

    $(document).on('click', '.qty-minus', function () {
        let input = $('#quantity');
        let val = Number(input.val()) || 1;
        if (val > 1) input.val(val - 1);
        updateTotalPrice();
    });

    $(document).on('change', '.option-input', function () {
        let parentSection = $(this).closest('.product-section');

        if ($(this).attr('type') === 'checkbox') {
            let max = Number(parentSection.data('max')) || 0;
            let checkedCount = parentSection.find('input[type="checkbox"]:checked').length;
            if (max && checkedCount > max) {
                $(this).prop('checked', false);
                showError(`Maximum ${max} selections allowed`);
            }
        }

        updateTotalPrice();
    });

    $(document).on('submit', '#productForm', function (e) {
        e.preventDefault();

        let attributeSelect = $('input[name="attribute_select"]:checked').val();
        let hasAttribute = $('[name="attribute_select"]').length > 0;

        if (hasAttribute && !attributeSelect) {
            showError('Please select how you want this product (On its own / With options)');
            return;
        }

        let productId = Number($('#productId').val()) || null;
        let skuRef = $('#productSkuRef').val() || '';
        if (!productId) {
            return;
        }

        if (hasAttribute && attributeSelect === 'standalone') {
            let cart = sanitizeCart();
            let attributePrice = Number($('[data-attribute-price]').data('attribute-price')) || 0;
            let basePrice = Number($('#totalPrice').data('base-price')) || 0;
            let qty = Number($('#quantity').val()) || 1;

            let existingItem = cart.find(item => 
                item.productId === productId && 
                item.type === 'direct_with_attribute'
            );

            if (existingItem) {
                existingItem.quantity += qty;
            } else {
                cart.push({
                    productId: productId,
                    skuRef: skuRef,
                    id: productId + '-standalone',
                    title: $('#productTitle').text().trim(),
                    image: $('#productImage').attr('src'),
                    price: basePrice + attributePrice,
                    quantity: qty,
                    type: "direct_with_attribute",
                    attribute: true
                });
            }

            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartUI();
            bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
            showSuccess('Added to cart!');
            return;
        }

        let valid = true;
        let missingOptions = [];
        
        if (hasAttribute && attributeSelect === 'with_options') {
            $('#optionsContainer')
            .find('.product-section:not([data-attribute="1"])')
                .each(function () {
                let isRequired = Number($(this).data('required'));
                let hasSelection = $(this).find('input:checked').length > 0;
                
                if (isRequired && !hasSelection) {
                    let optionName = $(this).find('.product-section-title').text().trim();
                    missingOptions.push(optionName);
                    valid = false;
                }
            });
        } else if (!hasAttribute) {
            $('.product-section').each(function () {
                let isRequired = Number($(this).data('required'));
                let hasSelection = $(this).find('input:checked').length > 0;
                
                if (isRequired && !hasSelection) {
                    let optionName = $(this).find('.product-section-title').text().trim();
                    missingOptions.push(optionName);
                    valid = false;
                }
            });
        }

        if (!valid) {
            $('.product-section').removeClass('option-error');

            let firstError = null;

            missingOptions.forEach(name => {
                let section = $('.product-section').filter(function () {
                    return $(this).find('.product-section-title').text().trim() === name;
                });

                section.addClass('option-error');

                if (!firstError) firstError = section;
            });

            if (firstError && firstError.length) {
                firstError[0].scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }

            showError('Please select required option(s)');
            return;
        }

        let cart = sanitizeCart();
        let options = {};
        let extraPrice = 0;
        let attributePrice = 0;

        if (hasAttribute && attributeSelect === 'with_options') {
            attributePrice = Number($('[data-attribute-price]').data('attribute-price')) || 0;
        }

        if (hasAttribute && attributeSelect === 'with_options') {
            $('#optionsContainer').find('.option-input:checked').each(function () {
                let label = $(this).data('title');
                let price = Number($(this).data('price')) || 0;
                let productId = Number($(this).data('product-id')) || null;
                let hubriseOptionRef = $(this).data('hubrise-option-ref') || '';
                extraPrice += price;

                let name = $(this).attr('name');
                if (!options[name]) options[name] = [];
                options[name].push({ 
                    title: label, 
                    price: price, 
                    productId: productId,
                    hubriseOptionRef: hubriseOptionRef
                });
            });
        } else if (!hasAttribute) {
            $('.option-input:checked').each(function () {
                let label = $(this).data('title');
                let price = Number($(this).data('price')) || 0;
                let productId = Number($(this).data('product-id')) || null;
                let hubriseOptionRef = $(this).data('hubrise-option-ref') || '';
                extraPrice += price;

                let name = $(this).attr('name');
                if (!options[name]) options[name] = [];
                options[name].push({ 
                    title: label, 
                    price: price, 
                    productId: productId,
                    hubriseOptionRef: hubriseOptionRef
                });
            });
        }

        let qty = Number($('#quantity').val()) || 1;
        let basePrice = Number($('#totalPrice').data('base-price')) || 0;
        let productTitle = String($('#productTitle').text() || '').trim();
        let productImage = String($('#productImage').attr('src') || '').trim();
        let finalPrice = basePrice + extraPrice + attributePrice;

        let productHash = createProductHash(productId, productTitle, options);

        let existingItem = cart.find(item => 
            item.type === 'custom' && 
            item.productHash === productHash &&
            item.attributePrice === attributePrice
        );

        if (existingItem) {
            existingItem.quantity += qty;
            showSuccess('Updated quantity in cart!');
        } else {
            cart.push({
                productId: productId,
                skuRef: skuRef,
                id: productId + '-' + Date.now(),
                productHash: productHash,
                title: productTitle,
                image: productImage,
                price: finalPrice,
                quantity: qty,
                options: options,
                type: "custom",
                attribute: hasAttribute && attributeSelect === 'with_options' ? true : false,
                attributePrice: attributePrice
            });
            showSuccess('Added to cart!');
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
        bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
    });

    $(document).on('click', '.cart-qty-plus', function () {
        let index = $(this).data('index');
        let cart = sanitizeCart();
        cart[index].quantity += 1;
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
    });

    $(document).on('click', '.cart-qty-minus', function () {
        let index = $(this).data('index');
        let cart = sanitizeCart();

        if (cart[index].quantity > 1) {
            cart[index].quantity -= 1;
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
        } else {
            showConfirm('Remove this item from cart?', function () {
                cart.splice(index, 1);
                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
            });
        }
    });

    $(document).on('click', '.cart-remove-btn', function () {
        let index = $(this).data('index');
        let cart = sanitizeCart();

        showConfirm('Remove this item from cart?', function () {
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
        });
    });

    $('#deliveryToggle').on('click', function() {
        const section = $(this).closest('.collapsible-section');
        const content = section.find('.collapsible-content');
        const icon = $(this).find('.collapsible-icon');
        content.toggleClass('hidden');
        icon.toggleClass('open');
    });

    $('#productsToggle').on('click', function() {
        const section = $(this).closest('.collapsible-section');
        const content = section.find('.collapsible-content');
        const icon = $(this).find('.collapsible-icon');
        content.toggleClass('hidden');
        icon.toggleClass('open');
    });

    $('#cartFloatBtn').on('click', function () {
        renderCart();
        showSubscriptionPromo();
        $('#cartOffcanvas').addClass('open');
        $('#cartOverlay').addClass('open');
    });

    $('#cartCloseBtn, #cartOverlay').on('click', function () {
        $('#cartOffcanvas').removeClass('open');
        $('#cartOverlay').removeClass('open');
    });

    $(document).on('change', 'input[name="deliveryType"]', function() {
        selectedDelivery.type = $(this).val();
        selectedDelivery.time = '';
        
        if ($(this).val() === 'delivery') {
            $('#deliveryMode').show();
            $('#collectionMode').hide();
            populateTimeSlots('#deliveryMode .delivery-time-select', 16, 30);
            $('#deliveryMode .delivery-time-select').val('');
            selectedDelivery.isValid = false;
            selectedDelivery.charge = 0;
            selectedDelivery.postcode = '';
        } else {
            $('#deliveryMode').hide();
            $('#collectionMode').show();
            populateTimeSlots('#collectionMode .delivery-time-select', 16, 30);
            $('#collectionMode .delivery-time-select').val('');
            selectedDelivery.isValid = true;
            selectedDelivery.charge = 0;
            selectedDelivery.postcode = '';
        }
        saveDeliveryData();
        updateCartUI();
    });

    $(document).on('click', '.postcode-check-btn', function(e) {
        e.preventDefault();
        let postcode = $('#deliveryPostcode').val().trim().toUpperCase();
        
        if (!postcode) {
            showError('Please enter a postcode');
            return;
        }

        $('.postcode-check-btn').prop('disabled', true).text('CHECKING...');

        $.ajax({
            url: 'https://api.postcodes.io/postcodes/' + postcode,
            type: 'GET',
            success: function(res) {
                let latitude = res.result.latitude;
                let longitude = res.result.longitude;

                $.ajax({
                    url: '/check-delivery',
                    type: 'GET',
                    data: {
                        postcode: postcode,
                        latitude: latitude,
                        longitude: longitude
                    },
                    success: function(res) {
                        selectedDelivery.postcode = postcode;
                        selectedDelivery.charge = parseFloat(res.delivery_charge);
                        selectedDelivery.isValid = true;
                        showSuccess('✓ Delivery available for ' + postcode + ' | Charge: £' + parseFloat(res.delivery_charge).toFixed(2));
                        saveDeliveryData();
                        updateCartUI();
                        $('.postcode-check-btn').prop('disabled', false).text('CHECK');
                    },
                    error: function(xhr) {
                        selectedDelivery.isValid = false;
                        selectedDelivery.postcode = '';
                        selectedDelivery.charge = 0;
                        saveDeliveryData();
                        showError('✗ Outside delivery area');
                        $('.postcode-check-btn').prop('disabled', false).text('CHECK');
                    }
                });
            },
            error: function() {
                showError('Invalid postcode');
                $('.postcode-check-btn').prop('disabled', false).text('CHECK');
            }
        });
    });

    $(document).on('change', '.delivery-time-select', function() {
        if (selectedDelivery.type === 'delivery') {
            if ($('#deliveryMode .delivery-time-select').length) {
                selectedDelivery.time = $('#deliveryMode .delivery-time-select').val();
            }
        } else {
            if ($('#collectionMode .delivery-time-select').length) {
                selectedDelivery.time = $('#collectionMode .delivery-time-select').val();
            }
        }
        saveDeliveryData();
        updateCartUI();
    });

    $(document).on('click', '.cart-checkout-btn', function(e) {
        e.preventDefault();
        
        let cart = sanitizeCart();
        let subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

        let hasPetFood = cart.some(item => {
            let category = (item.category || '').toLowerCase();
            return category.includes('treats for furry friends') ||
                category.includes('furry') ||
                category.includes('pet');
        });

        if (!hasPetFood) {
            showAlertModal(
                'Have you forgotten about your Dog/Cat?',
                'Add tasty treats for your furry friends',
                () => {
                    $('#cartOffcanvas').removeClass('open');
                    $('#cartOverlay').removeClass('open');
                    loadPetTreatsModal();
                },
                "Add Dog/Cat Meal",
                "Proceed to Checkout"
            );
            return;
        }

        if (subtotal < 25) {
            let hasFriesOrDrinks = cart.some(item => {
                let category = (item.category || '').toLowerCase();
                return category.includes('fries') || category.includes('drinks');
            });
            
            if (!hasFriesOrDrinks) {
                showAlertModal(
                    'Have you forgotten to add Fries or Drinks?',
                    'Complete your meal with delicious sides and beverages',
                    () => {
                        $('#cartOffcanvas').removeClass('open');
                        $('#cartOverlay').removeClass('open');
                        $('#categoryPills .pill[data-filter="fries"]').click();
                    },
                    "Add Fries/Drinks",
                    "Proceed to Checkout"
                );
                return;
            }
        }
        
        continueCheckout();
    });

    function loadPetTreatsModal() {
        showLoader('Finding treats for your furry friends...');

        $.ajax({
            url: '/get-category-products',
            type: 'GET',
            data: { 
                category_name: 'Treats for Furry Friends' 
            },
            dataType: 'json',
            success: function(response) {
                hideLoader();

                if (!response.success || !response.products || response.products.length === 0) {
                    showError('No pet treats available at the moment.');
                    return;
                }

                let html = '<div id="petTreatsModal" style="position:fixed; top:50%; left:50%; transform:translate(-50%, -50%); background:#fff; width:90%; max-width:520px; border-radius:16px; box-shadow:0 20px 50px rgba(0,0,0,0.4); z-index:99999; overflow:hidden; font-family:Arial, sans-serif;">';

                html += `
                    <div style="padding:20px; border-bottom:1px solid #eee; display:flex; justify-content:space-between; align-items:center; background:#f8f9fa;">
                        <h4 style="margin:0; color:#000;">
                            ${response.category_name || 'Treats for Furry Friends'}
                        </h4>
                        <button class="close-pet-modal" style="font-size:28px; background:none; border:none; cursor:pointer; color:#999;">×</button>
                    </div>
                    
                    <div style="padding:20px; max-height:65vh; overflow-y:auto;">
                        <p style="margin:0 0 18px 0; color:#000; font-size:15px;">Select treats for your dog or cat:</p>
                        <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap:16px;">`;

                let currentCart = sanitizeCart();

                response.products.forEach(function(product) {
                    const isInCart = currentCart.some(function(item) {
                        return Number(item.productId) === Number(product.id) && item.type === 'direct';
                    });

                    html += `
                        <div style="border:1px solid #ddd; border-radius:10px; padding:12px; position:relative; background:#fff;">
                            <div style="height:130px; margin-bottom:12px; border-radius:8px; overflow:hidden;">
                                <img src="${product.image}" alt="${product.title}" 
                                    style="width:100%; height:100%; object-fit:cover;" 
                                    onerror="this.src='/placeholder.webp'">
                            </div>
                            <div style="font-size:15px; font-weight:600; margin-bottom:8px; line-height:1.3;">
                                ${product.title}
                            </div>
                            <div style="color:#ff8a00; font-weight:bold; font-size:16px;">
                                £${parseFloat(product.price).toFixed(2)}
                            </div>
                            
                            <label style="position:absolute; top:15px; right:15px; cursor:pointer;">
                                <input type="checkbox" class="pet-treat-checkbox"
                                    data-id="${product.id}"
                                    data-title="${product.title}"
                                    data-price="${product.price}"
                                    data-image="${product.image}"
                                    data-sku-ref="${product.sku_ref || ''}"
                                    data-category="Treats for Furry Friends"
                                    ${isInCart ? 'checked disabled' : ''}
                                    style="width:22px; height:22px; accent-color:#ff8a00;">
                            </label>
                        </div>`;
                });

                html += `</div></div>
                    
                    <div style="padding:18px 20px; border-top:1px solid #eee; display:flex; gap:12px;">
                        <button id="cancelPetTreats" style="flex:1; padding:12px; background:#f1f1f1; border:none; border-radius:8px; font-weight:600; cursor:pointer;">Cancel</button>
                        <button id="addSelectedPetTreats" style="flex:1; padding:12px; background:linear-gradient(135deg, #ff8a00, #ff5a00); color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer;">Add Selected Treats</button>
                    </div>
                </div>`;

                $('body').append(html);

                $('.close-pet-modal, #cancelPetTreats').on('click', function() {
                    $('#petTreatsModal').remove();
                });

                $('#addSelectedPetTreats').on('click', function() {
                    let selected = [];
                    
                    $('.pet-treat-checkbox:checked').each(function() {
                        if (!$(this).prop('disabled')) {
                            selected.push({
                                productId: Number($(this).data('id')),
                                title: $(this).data('title'),
                                price: parseFloat($(this).data('price')),
                                image: $(this).data('image'),
                                skuRef: $(this).data('sku-ref') || '',
                                category: $(this).data('category')
                            });
                        }
                    });

                    if (selected.length === 0) {
                        showError('Please select at least one treat');
                        return;
                    }

                    addMultipleToCart(selected);
                    $('#petTreatsModal').remove();
                    renderCart();
                    showSuccess(selected.length + ' treat(s) added to cart!');
                });
            },
            error: function(xhr) {
                hideLoader();
                showError('Failed to load pet treats. Please try again.');
            }
        });
    }

    function addMultipleToCart(items) {
        let cart = sanitizeCart();

        items.forEach(item => {
            let existing = cart.find(cartItem => 
                Number(cartItem.productId) === Number(item.productId) && 
                cartItem.type === 'direct'
            );

            if (existing) {
                existing.quantity += 1;
            } else {
                cart.push({
                    productId: item.productId,
                    id: item.productId,
                    title: String(item.title || '').trim(),
                    image: String(item.image || '').trim(),
                    price: Number(item.price) || 0,
                    skuRef: String(item.skuRef || '').trim(),
                    category: String(item.category || '').trim(),
                    quantity: 1,
                    type: "direct"
                });
            }
        });

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
    }

    $(document).on('change', 'input[name="attribute_select"]', function() {
        if ($(this).val() === 'with_options') {
            $('#optionsContainer').slideDown();
            $('.attribute-input').not(this).prop('checked', false);
        } else {
            $('#optionsContainer').slideUp();
            $('#optionsContainer').find('.option-input').prop('checked', false);
        }
        updateTotalPrice();
    });

    $(document).on('click', '.btn-order-again', function () {
        let order = $(this).data('order');

        if (!order || !order.items || !order.items.length) {
            showError('No items found in this order!');
            return;
        }

        let cart = sanitizeCart();

        order.items.forEach(item => {
            let isCustom = item.options && item.options.length > 0;
            let optionsObj = {};

            if (isCustom) {
                item.options.forEach(opt => {
                    if (!optionsObj[opt.option_list_name]) optionsObj[opt.option_list_name] = [];
                    optionsObj[opt.option_list_name].push({
                        title: opt.option_name,
                        price: parseFloat(opt.price),
                        productId: parseInt(item.product_id) || null,
                        hubriseOptionRef: opt.option_ref || ''
                    });
                });
            }

            let productHash = isCustom ? item.productHash || item.id + '-' + Date.now() : null;

            let existingItem = cart.find(c => {
                if (isCustom) return c.type === 'custom' && c.productHash === productHash;
                else return c.productId === item.product_id && c.type === 'direct';
            });

            let qty = parseInt(item.quantity) || 1;
            let price = parseFloat(item.price);

            if (existingItem) {
                existingItem.quantity += qty;
            } else {
                cart.push({
                    productId: item.product_id,
                    skuRef: item.sku_ref || '',
                    id: item.id + '-' + Date.now(),
                    productHash: productHash,
                    title: item.product_name,
                    image: item.product?.image || '',
                    price: price,
                    quantity: qty,
                    options: optionsObj,
                    type: isCustom ? 'custom' : 'direct',
                    attribute: item.product?.has_attribute || false,
                    attributePrice: item.product?.attribute_price || 0
                });
            }
        });

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
        showSuccess('Items added from previous order!');
    });

    function initLinkedOptionFilter() {
        const title = $('#productTitle').text().trim().toLowerCase();
        console.log('Product title:', JSON.stringify(title));
        if (title.indexOf('combo kebab') === -1) return;
        const sections = [];
        $('.product-section[data-option-id]').each(function() {
            sections.push($(this));
        });

        if (sections.length < 2) return;

        for (let i = 0; i < sections.length; i++) {
            for (let j = i + 1; j < sections.length; j++) {
                const aIds = sections[i].find('.option-input').map(function(){ return $(this).val(); }).get().sort().join(',');
                const bIds = sections[j].find('.option-input').map(function(){ return $(this).val(); }).get().sort().join(',');

                if (aIds === bIds) {
                    bindLinkedSections(sections[i], sections[j]);
                }
            }
        }
    }

    function bindLinkedSections(sectionA, sectionB) {
        function syncFilter(changed, other) {
            const checkedVal = changed.find('.option-input:checked').val();
            other.find('.option-item').show();
            if (checkedVal) {
                other.find('.option-input[value="' + checkedVal + '"]').closest('.option-item').hide();
                const conflict = other.find('.option-input[value="' + checkedVal + '"]:checked');
                if (conflict.length) {
                    conflict.prop('checked', false);
                }
            }
            updateTotalPrice();
        }

        sectionA.on('change', '.option-input', function() { syncFilter(sectionA, sectionB); });
        sectionB.on('change', '.option-input', function() { syncFilter(sectionB, sectionA); });
    }

    loadDeliveryData();
    updateDeliveryStartTimes();
    populateTimeSlots('#deliveryMode .delivery-time-select', 16, 30);
    populateTimeSlots('#collectionMode .delivery-time-select', 16, 30);
    updateCartUI();
});