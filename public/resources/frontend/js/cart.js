$(function () {

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
            image: String(item.image || '').trim()
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

        let existingItem = cart.find(item => item.id === productId && item.type === 'direct');
        if (existingItem) existingItem.quantity += 1;
        else {
            cart.push({
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

        // Check if has attribute
        let attributeSelect = $('input[name="attribute_select"]:checked').val();
        let hasAttribute = $('[name="attribute_select"]').length > 0;

        // Add attribute price if "with_options" selected
        if (hasAttribute && attributeSelect === 'with_options') {
            attributePrice = Number($('[data-attribute-price]').data('attribute-price')) || 0;
        }

        // Get option prices
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

    /* UPDATED: Handle form submit with attribute logic and pinpoint validation */
    $(document).on('submit', '#productForm', function (e) {
        e.preventDefault();

        let attributeSelect = $('input[name="attribute_select"]:checked').val();
        let hasAttribute = $('[name="attribute_select"]').length > 0;

        // Case 1: Has attribute and selected "On its own" (standalone)
        if (hasAttribute && attributeSelect === 'standalone') {
            let cart = sanitizeCart();
            let productId = $('#productTitle').text() + '-standalone-' + Date.now();
            let attributePrice = Number($('[data-attribute-price]').data('attribute-price')) || 0;
            let basePrice = Number($('#totalPrice').data('base-price')) || 0;
            let qty = Number($('#quantity').val()) || 1;

            cart.push({
                id: productId,
                title: String($('#productTitle').text() || '').trim(),
                image: String($('#productImage').attr('src') || '').trim(),
                price: basePrice + attributePrice,
                quantity: qty,
                type: "direct_with_attribute",
                attribute: true
            });

            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartUI();
            bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
            showSuccess('Added to cart!');
            return;
        }

        // Case 2: Has attribute and selected "With options" OR no attribute at all
        // Validate required options with pinpoint error messages
        let valid = true;
        let missingOptions = [];
        
        if (hasAttribute && attributeSelect === 'with_options') {
            // Validate only options in container for attribute case
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
            // Validate all options for non-attribute case
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

        // Get attribute price if "with options" is selected
        if (hasAttribute && attributeSelect === 'with_options') {
            attributePrice = Number($('[data-attribute-price]').data('attribute-price')) || 0;
        }

        // Get selected options
        if (hasAttribute && attributeSelect === 'with_options') {
            $('#optionsContainer').find('.option-input:checked').each(function () {
                let label = $(this).data('title');
                let price = Number($(this).data('price')) || 0;
                extraPrice += price;

                let name = $(this).attr('name');
                if (!options[name]) options[name] = [];
                options[name].push({ title: label, price: price });
            });
        } else if (!hasAttribute) {
            $('.option-input:checked').each(function () {
                let label = $(this).data('title');
                let price = Number($(this).data('price')) || 0;
                extraPrice += price;

                let name = $(this).attr('name');
                if (!options[name]) options[name] = [];
                options[name].push({ title: label, price: price });
            });
        }

        let qty = Number($('#quantity').val()) || 1;
        let basePrice = Number($('#totalPrice').data('base-price')) || 0;

        cart.push({
            id: $('#productTitle').text() + '-' + Date.now(),
            title: String($('#productTitle').text() || '').trim(),
            image: String($('#productImage').attr('src') || '').trim(),
            price: basePrice + extraPrice + attributePrice,
            quantity: qty,
            options: options,
            type: "custom",
            attribute: hasAttribute && attributeSelect === 'with_options' ? true : false,
            attributePrice: attributePrice
        });

        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();

        bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
        showSuccess('Added to cart!');
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
            if (item.type === 'custom' && item.options) {
                optionsHTML = '<ul style="margin: 8px 0; padding-left: 20px; font-size: 12px; color: #999;">';
                Object.values(item.options).forEach(optArr => {
                    optArr.forEach(opt => {
                        optionsHTML += `<li>${escapeHtml(opt.title)}${opt.price > 0 ? ' (+£' + Number(opt.price).toFixed(2) + ')' : ''}</li>`;
                    });
                });
                optionsHTML += '</ul>';
            }

            html += `
            <div class="cart-item-row">
                <div class="row g-2 align-items-start">
                    <div class="col-2">
                        <img src="${escapeHtml(item.image)}" class="cart-item-img" alt="${escapeHtml(item.title)}">
                    </div>
                    <div class="col-10 position-relative">
                        <div class="row">
                            <div class="col-10">
                                <p class="cart-product-name mb-1">${escapeHtml(item.title)}</p>
                                ${optionsHTML}
                            </div>
                            <div class="col-2">
                                <button class="cart-remove-btn position-absolute top-0 end-0" data-index="${index}">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6 text-start">
                                <span class="cart-product-price">£${Number(item.price).toFixed(2)}</span>
                            </div>
                            <div class="col-6 text-end">
                                <div class="cart-qty-control">
                                    <button class="cart-qty-btn cart-qty-minus" data-index="${index}" aria-label="Decrease quantity">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="cart-qty-display">${Number(item.quantity)}</span>
                                    <button class="cart-qty-btn cart-qty-plus" data-index="${index}" aria-label="Increase quantity">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
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
        if (cart[index].quantity > 1) cart[index].quantity -= 1;
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
        updateCartUI();
    });

    $(document).on('click', '.cart-remove-btn', function () {
        let index = $(this).data('index');
        let cart = sanitizeCart();
        cart.splice(index, 1);
        localStorage.setItem('cart', JSON.stringify(cart));
        renderCart();
        updateCartUI();
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

    // Handle attribute selection - show/hide options
    $(document).on('change', 'input[name="attribute_select"]', function() {
        if ($(this).val() === 'with_options') {
            $('#optionsContainer').slideDown();
            // Clear previous selections
            $('.attribute-input').not(this).prop('checked', false);
        } else {
            $('#optionsContainer').slideUp();
            // Clear all options
            $('#optionsContainer').find('.option-input').prop('checked', false);
        }
        updateTotalPrice();
    });

    function updateCartUI() {
        let cart = sanitizeCart();
        let totalQty = cart.reduce((sum, item) => sum + item.quantity, 0);
        $('.cart-badge').text(totalQty);

        let subtotal = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        $('#cartSubtotal').text('£' + subtotal.toFixed(2));
    }

    updateCartUI();

});