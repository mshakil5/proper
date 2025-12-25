@extends('frontend.master')

@section('content')
<div class="checkout-container">
    <div class="container py-5">
        <div class="row g-4">
            <!-- Left Column: Forms -->
            <div class="col-lg-8">
                <div class="checkout-card">
                    <h5 class="checkout-title">Delivery Details</h5>
                    <form>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" placeholder="John">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" placeholder="Doe">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="john@example.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" placeholder="+44 123 456 7890">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <input type="text" class="form-control" placeholder="123 Main Street">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">City</label>
                                <input type="text" class="form-control" placeholder="London">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Postal Code</label>
                                <input type="text" class="form-control" placeholder="SW1A 1AA">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="checkout-card">
                    <h5 class="checkout-title">Payment Method</h5>
                    <div class="payment-options">
                        <div class="payment-option active">
                            <input type="radio" name="payment" id="card" value="card" checked>
                            <label for="card" class="payment-label">
                                <i class="fas fa-credit-card"></i>
                                <span>Credit / Debit Card</span>
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" name="payment" id="paypal" value="paypal">
                            <label for="paypal" class="payment-label">
                                <i class="fab fa-paypal"></i>
                                <span>PayPal</span>
                            </label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" name="payment" id="apple" value="apple">
                            <label for="apple" class="payment-label">
                                <i class="fab fa-apple"></i>
                                <span>Apple Pay</span>
                            </label>
                        </div>
                    </div>

                    <div id="cardPayment" class="payment-form">
                        <div class="row g-3 mt-3">
                            <div class="col-12">
                                <label class="form-label">Cardholder Name</label>
                                <input type="text" class="form-control" placeholder="John Doe">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Card Number</label>
                                <input type="text" class="form-control" placeholder="1234 5678 9012 3456">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Expiry Date</label>
                                <input type="text" class="form-control" placeholder="MM/YY">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">CVV</label>
                                <input type="text" class="form-control" placeholder="123">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Order Summary -->
            <div class="col-lg-4">
                <div class="order-summary">
                    <h5 class="checkout-title">Order Summary</h5>

                    <!-- Cart Items -->
                    <div class="summary-items">
                        <div class="summary-item">
                            <div class="item-details">
                                <span class="item-name">French Vanilla Fantasy</span>
                                <span class="item-qty">x2</span>
                            </div>
                            <span class="item-price">£25.66</span>
                        </div>
                        <div class="summary-item">
                            <div class="item-details">
                                <span class="item-name">Chocolate Bliss</span>
                                <span class="item-qty">x1</span>
                            </div>
                            <span class="item-price">£12.83</span>
                        </div>
                    </div>

                    <div class="summary-divider"></div>

                    <!-- Pricing Breakdown -->
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>£38.49</span>
                    </div>
                    <div class="summary-row">
                        <span>Delivery</span>
                        <span>£2.50</span>
                    </div>
                    <div class="summary-row">
                        <span>Tax</span>
                        <span>£8.20</span>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-row total">
                        <span>Total</span>
                        <span>£49.19</span>
                    </div>

                    <button class="btn-place-order">Place Order</button>

                    <div class="secure-badge">
                        <i class="fas fa-lock"></i>
                        <span>Secure & Encrypted</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<style>
.checkout-container {
    background: linear-gradient(135deg, #fff8f0, #fff3e7);
    min-height: 100vh;
    padding: 40px 0;
}

.checkout-card {
    background: white;
    border-radius: 12px;
    padding: 28px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    margin-bottom: 24px;
    border: 1px solid rgba(0, 0, 0, 0.04);
}

.checkout-title {
    font-size: 18px;
    font-weight: 700;
    color: #1a1a1a;
    margin-bottom: 20px;
}

.form-control {
    border: 1px solid #E8E8E8;
    border-radius: 8px;
    padding: 12px 14px;
    font-size: 14px;
    transition: all 0.2s ease;
}

.form-control:focus {
    border-color: #ff8a00;
    box-shadow: 0 0 0 3px rgba(255, 138, 0, 0.1);
    outline: none;
}

.form-label {
    font-size: 13px;
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 8px;
}

.payment-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
    margin-bottom: 20px;
}

.payment-option {
    position: relative;
}

.payment-option input {
    display: none;
}

.payment-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 16px;
    border: 2px solid #E8E8E8;
    border-radius: 10px;
    cursor: pointer;
    background: white;
    transition: all 0.2s ease;
    margin: 0;
}

.payment-label i {
    font-size: 24px;
    color: #666;
}

.payment-label span {
    font-size: 12px;
    font-weight: 600;
    color: #666;
    text-align: center;
}

.payment-option input:checked + .payment-label {
    border-color: #ff8a00;
    background: #fff8f0;
}

.payment-option input:checked + .payment-label i {
    color: #ff8a00;
}

.payment-option input:checked + .payment-label span {
    color: #ff8a00;
}

.order-summary {
    background: white;
    border-radius: 12px;
    padding: 28px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
    border: 1px solid rgba(0, 0, 0, 0.04);
    position: sticky;
    top: 20px;
}

.summary-items {
    margin-bottom: 20px;
    max-height: 300px;
    overflow-y: auto;
}

.summary-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 12px;
    padding: 12px 0;
}

.item-details {
    display: flex;
    flex-direction: column;
    gap: 4px;
    flex: 1;
}

.item-name {
    font-size: 13px;
    font-weight: 600;
    color: #1a1a1a;
}

.item-qty {
    font-size: 12px;
    color: #999;
}

.item-price {
    font-size: 13px;
    font-weight: 600;
    color: #ff8a00;
}

.summary-divider {
    height: 1px;
    background: #E8E8E8;
    margin: 16px 0;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    font-size: 13px;
    color: #666;
    margin-bottom: 12px;
}

.summary-row span:last-child {
    font-weight: 600;
    color: #1a1a1a;
}

.summary-row.total {
    font-size: 16px;
    font-weight: 700;
    color: #1a1a1a;
    margin-top: 16px;
}

.summary-row.total span:last-child {
    color: #ff8a00;
}

.btn-place-order {
    width: 100%;
    padding: 14px;
    background: linear-gradient(135deg, #ff8a00, #ff5a00);
    color: white;
    border: none;
    border-radius: 10px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    margin-top: 20px;
    transition: all 0.2s ease;
}

.btn-place-order:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(255, 138, 0, 0.3);
}

.secure-badge {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #E8E8E8;
    font-size: 12px;
    color: #666;
}

.secure-badge i {
    color: #27AE60;
}

@media (max-width: 992px) {
    .order-summary {
        position: static;
        top: auto;
    }
}

@media (max-width: 576px) {
    .checkout-card,
    .order-summary {
        padding: 20px;
    }

    .payment-options {
        grid-template-columns: 1fr;
    }

    .checkout-title {
        font-size: 16px;
        margin-bottom: 16px;
    }
}
</style>