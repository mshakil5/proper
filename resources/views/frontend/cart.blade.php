<button type="button" class="cart-float-btn" id="cartFloatBtn">
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

    <div class="cart-body" id="cartBody">
    </div>

    <div class="cart-footer">
        <div class="cart-summary-row">
            <span class="cart-product-name">Subtotal</span>
            <span class="cart-total-price" id="cartSubtotal">£0.00</span>
        </div>
        <a href="#" class="cart-checkout-btn">Proceed to Checkout</a>
    </div>
</div>