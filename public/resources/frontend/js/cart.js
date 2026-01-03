$(function () {

    let selectedDelivery = {
        type: 'delivery',
        postcode: '',
        charge: 0,
        time: '',
        isValid: false
    };

    function loadDeliveryData() {
        let stored = localStorage.getItem('deliveryOptions');
        if (stored) {
            selectedDelivery = JSON.parse(stored);
            if (selectedDelivery.type === 'collection') {
                $('input[name="deliveryType"][value="collection"]').prop('checked', true);
                $('#deliveryMode').hide();
                $('#collectionMode').show();
            } else {
                $('input[name="deliveryType"][value="delivery"]').prop('checked', true);
                $('#deliveryMode').show();
                $('#collectionMode').hide();
                if (selectedDelivery.postcode) {
                    $('#deliveryPostcode').val(selectedDelivery.postcode);
                }
            }
            if (selectedDelivery.time) {
                $('select.delivery-time-select').val(selectedDelivery.time);
            }
        }
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
        let current = new Date();
        current.setHours(startHour, startMinute, 0);
        
        let endTime = new Date();
        endTime.setHours(23, 5, 0);
        
        while (current < endTime) {
            let start = current.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
            
            let end = new Date(current);
            end.setMinutes(end.getMinutes() + 20);
            let endSlot = end.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
            
            slots.push({
                value: start + '-' + endSlot,
                label: start + ' - ' + endSlot
            });
            
            current.setMinutes(current.getMinutes() + 20);
        }
        
        return slots;
    }

    function populateTimeSlots(selector, startHour, startMinute) {
        let slots = generateTimeSlots(startHour, startMinute);
        let today = new Date().toLocaleDateString('en-US', { weekday: 'long' });
        
        let html = '<option value="">Select Time</option>';
        slots.forEach(slot => {
            html += `<option value="${slot.value}">${slot.label} (${today})</option>`;
        });
        
        $(selector).html(html);
    }

    function updateDeliveryStartTimes() {
        let deliverySlots = generateTimeSlots(16, 45);
        let collectionSlots = generateTimeSlots(16, 15);
        
        if (deliverySlots.length > 0) {
            $('#deliveryStartTime').text(deliverySlots[0].label.split(' - ')[0]);
        }
        
        if (collectionSlots.length > 0) {
            $('#collectionStartTime').text(collectionSlots[0].label.split(' - ')[0]);
        }
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
                },
                error: function (err) {
                    console.error('Error loading product:', err);
                }
            });
        }
    });

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
                quantity: 1,
                type: "direct"
            });
        }

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
        showSuccess('Added to cart!');
    }

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
            let max = Number(parentSection.data('max')) || 999;
            let checkedCount = parentSection.find('input[type="checkbox"]:checked').length;
            if (max && checkedCount > max) {
                $(this).prop('checked', false);
                showError(`Maximum ${max} selections allowed`);
            }
        }

        updateTotalPrice();
    });

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

    $(document).on('submit', '#productForm', function (e) {
        e.preventDefault();

        let attributeSelect = $('input[name="attribute_select"]:checked').val();
        let hasAttribute = $('[name="attribute_select"]').length > 0;

        if (hasAttribute && !attributeSelect) {
            showError('Please select how you want this product (On its own / With options)');
            return;
        }

        let productId = Number($('#productId').val()) || null;
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
            $('#optionsContainer').find('.product-section').each(function () {
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
            if (missingOptions.length === 1) {
                showError(`Please select: ${missingOptions[0]}`);
            } else {
                showError(`Please select: ${missingOptions.join(', ')}`);
            }
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
                extraPrice += price;

                let name = $(this).attr('name');
                if (!options[name]) options[name] = [];
                options[name].push({ title: label, price: price, productId: productId });
            });
        } else if (!hasAttribute) {
            $('.option-input:checked').each(function () {
                let label = $(this).data('title');
                let price = Number($(this).data('price')) || 0;
                let productId = Number($(this).data('product-id')) || null;
                extraPrice += price;

                let name = $(this).attr('name');
                if (!options[name]) options[name] = [];
                options[name].push({ title: label, price: price, productId: productId });
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

    $(document).on('click', '.cart-qty-plus', function () {
        let index = $(this).data('index');
        let cart = sanitizeCart();
        cart[index].quantity += 1;
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
        updateCartUI();
    });

    $(document).on('click', '.cart-qty-minus', function () {
        let index = $(this).data('index');
        let cart = sanitizeCart();

        if (cart[index].quantity > 1) {
            cart[index].quantity -= 1;
            localStorage.setItem('cart', JSON.stringify(cart));
            renderCart();
            updateCartUI();
        } else {
            showConfirm('Remove this item from cart?', function () {
                cart.splice(index, 1);
                localStorage.setItem('cart', JSON.stringify(cart));
                renderCart();
                updateCartUI();
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
            updateCartUI();
        });
    });

    $('#cartFloatBtn').on('click', function () {
        renderCart();
        $('#cartOffcanvas').addClass('open');
        $('#cartOverlay').addClass('open');
    });

    $('#cartCloseBtn, #cartOverlay').on('click', function () {
        $('#cartOffcanvas').removeClass('open');
        $('#cartOverlay').removeClass('open');
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

    $(document).on('change', 'input[name="deliveryType"]', function() {
        selectedDelivery.type = $(this).val();
        selectedDelivery.time = '';
        
        if ($(this).val() === 'delivery') {
            $('#deliveryMode').show();
            $('#collectionMode').hide();
            populateTimeSlots('#deliveryMode .delivery-time-select', 16, 45);
            $('#deliveryMode .delivery-time-select').val('');
            selectedDelivery.isValid = false;
            selectedDelivery.charge = 0;
            selectedDelivery.postcode = '';
        } else {
            $('#deliveryMode').hide();
            $('#collectionMode').show();
            populateTimeSlots('#collectionMode .delivery-time-select', 16, 15);
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

        $.ajax({
            url: '/check-delivery',
            type: 'GET',
            data: {
                postcode: postcode
            },
            success: function(res) {
                console.log(res);
                if (res.available) {
                    selectedDelivery.postcode = postcode;
                    selectedDelivery.charge = parseFloat(res.delivery_charge);
                    selectedDelivery.isValid = true;
                    showSuccess('✓ Delivery available for ' + postcode + ' | Charge: £' + parseFloat(res.delivery_charge).toFixed(2));
                    saveDeliveryData();
                    updateCartUI();
                } else {
                    selectedDelivery.isValid = false;
                    selectedDelivery.postcode = '';
                    selectedDelivery.charge = 0;
                    saveDeliveryData();
                    showError('✗ Delivery not available for ' + postcode);
                }
            },
            error: function(xhr) {
                showError('Postcode not in delivery area');
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

        console.log('Checkout Data:', checkoutData);
        
        showSuccess('Proceeding to checkout...');
        
        setTimeout(() => {
            window.location.href = '/checkout';
        }, 1000);
    });

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
    }

    updateDeliveryStartTimes();
    loadDeliveryData();
    populateTimeSlots('#deliveryMode .delivery-time-select', 16, 45);
    populateTimeSlots('#collectionMode .delivery-time-select', 16, 15);
    updateCartUI();

});
