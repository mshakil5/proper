<button type="button" class="cart-float-btn d-none" id="cartFloatBtn">
    <i class="fas fa-shopping-cart"></i>
    <span class="cart-badge">0</span>
</button>

<div class="cart-overlay" id="cartOverlay"></div>

<div class="cart-offcanvas" id="cartOffcanvas">
    <div class="cart-header">
        <h5>Shopping Cart</h5>
        <button type="button" class="cart-close-btn" id="cartCloseBtn">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <div class="cart-scrollable-content">
        <div class="collapsible-section cart-delivery-section pt-3" id="deliverySection">
            <button type="button" class="collapsible-header" id="deliveryToggle">
                <span>Delivery Options</span>
                <span class="collapsible-icon open">
                    <i class="fas fa-chevron-up"></i>
                </span>
            </button>

            <div class="collapsible-content" id="deliveryContent">
                <div class="cart-delivery-header">
                    <div class="delivery-type-group">
                        <label class="delivery-type-option">
                            <input type="radio" name="deliveryType" value="delivery" checked>
                            <span class="delivery-type-icon">
                                <i class="fas fa-truck"></i>
                            </span>
                            <span class="delivery-type-text">
                                <strong>Home Delivery</strong>
                                <small>Starts: <span id="deliveryStartTime">04:45 pm</span></small>
                            </span>
                        </label>
                        
                        <label class="delivery-type-option">
                            <input type="radio" name="deliveryType" value="collection">
                            <span class="delivery-type-icon">
                                <i class="fas fa-shopping-bag"></i>
                            </span>
                            <span class="delivery-type-text">
                                <strong>Collection</strong>
                                <small>Starts: <span id="collectionStartTime">04:15 pm</span></small>
                            </span>
                        </label>
                    </div>
                </div>

                <div id="deliveryMode">
                    <div class="delivery-postcode-section">
                        <label class="delivery-label">Postcode</label>
                        <div class="postcode-input-group">
                            <input type="text" class="postcode-input" id="deliveryPostcode" placeholder="LN5 8ES">
                            <button type="button" class="postcode-check-btn">CHECK</button>
                        </div>
                    </div>

                    <div class="delivery-time-section">
                        <label class="delivery-label">Delivery Time <span class="required">*</span></label>
                        <select class="delivery-time-select">
                            <option value="">Select Time</option>
                            <option>04:45 - 05:05 PM</option>
                            <option>05:10 - 05:30 PM</option>
                            <option>05:35 - 05:55 PM</option>
                            <option>06:00 - 06:20 PM</option>
                        </select>
                    </div>
                </div>

                <div id="collectionMode" style="display: none;">
                    <div class="collection-time-section">
                        <label class="delivery-label">Collection Time <span class="required">*</span></label>
                        <select class="delivery-time-select">
                            <option value="">Select Time</option>
                            <option>04:15 - 04:35 PM</option>
                            <option>04:40 - 05:00 PM</option>
                            <option>05:05 - 05:25 PM</option>
                            <option>05:30 - 05:50 PM</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="collapsible-section" id="productsSection">
            <button type="button" class="collapsible-header" id="productsToggle">
                <span>Items <span id="itemCount" style="color: #ff8a00; font-weight: 800;">0</span></span>
                <span class="collapsible-icon open">
                    <i class="fas fa-chevron-up"></i>
                </span>
            </button>

            <div class="collapsible-content" id="productsContent">
                <div class="cart-body" id="cartBody"></div>
            </div>
        </div>
    </div>

    <div class="cart-footer">
        <div class="cart-summary-row">
            <span>Subtotal</span>
            <span class="cart-total-price" id="cartSubtotal">£0.00</span>
        </div>
        <div class="cart-summary-row">
            <span>Delivery Charge</span>
            <span class="cart-total-price" id="cartDeliveryCharge">£0.00</span>
        </div>
        <div class="cart-summary-row" style="border-top: 1px solid #E8E8E8; padding-top: 8px; margin-top: 8px; font-weight: 800; font-size: 14px;">
            <span>Total</span>
            <span class="cart-total-price" id="cartTotal">£0.00</span>
        </div>
        <a href="#" class="cart-checkout-btn">Proceed to Checkout</a>
    </div>
</div>