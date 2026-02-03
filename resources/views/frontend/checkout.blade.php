@extends('frontend.master')

@section('content')
    <div class="checkout-wrapper">
        <div class="container py-4">
            <div class="row g-4">
                <div class="col-lg-8">
                    @if (!auth()->check())
                        <div class="checkout-card">
                            <h5 class="checkout-title">Checkout As</h5>
                            {{-- Tab Buttons --}}
                            <div class="nav nav-tabs" role="tablist" id="customerTypeTabs">
                                <button class="nav-link {{ !$errors->any() ? 'active' : '' }}" id="guest-tab" data-bs-toggle="tab" data-bs-target="#guestPanel" type="button" role="tab">
                                    <i class="fas fa-user-secret"></i> Guest
                                </button>
                                <button class="nav-link {{ $errors->any() && !old('first_name') && !old('last_name') && !old('phone') ? 'active' : '' }}" id="existing-tab" data-bs-toggle="tab" data-bs-target="#existingPanel" type="button" role="tab">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </button>
                                <button class="nav-link {{ (old('first_name') || old('last_name') || old('phone')) && $errors->any() ? 'active' : '' }}" id="register-tab" data-bs-toggle="tab" data-bs-target="#registerPanel" type="button" role="tab">
                                    <i class="fas fa-user-plus"></i> Register
                                </button>
                            </div>
                        </div>
                        {{-- Tab Panels --}}
                        <div class="tab-content" id="customerTypeContent">
                            {{-- Guest Panel --}}
                            <div class="tab-pane fade {{ !$errors->any() ? 'show active' : '' }}" id="guestPanel" role="tabpanel">
                                <div class="checkout-card">
                                    <h5 class="checkout-title">Guest Details</h5>
                                    <div class="alert mb-3 text-white" style="background:#ff8a00;">
                                        <i class="fas fa-info-circle"></i>
                                        Checkout as guest – you may lose loyalty points and exclusive features.
                                    </div>
                                    <form id="guestForm">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">First Name <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="guestFirstName"
                                                    placeholder="" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Last Name <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="guestLastName"
                                                    placeholder="" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email <span class="required">*</span></label>
                                                <input type="email" class="form-control" id="guestEmail"
                                                    placeholder="" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone <span class="required">*</span></label>
                                                <input type="tel" class="form-control" id="guestPhone"
                                                    placeholder="" required>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            {{-- Login Panel --}}
                            <div class="tab-pane fade {{ $errors->any() && !old('first_name') && !old('last_name') && !old('phone') ? 'show active' : '' }}" id="existingPanel" role="tabpanel">
                                <div class="checkout-card">
                                    <h5 class="checkout-title">Login to Your Account</h5>
                                    <form id="loginForm" method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <input type="hidden" name="redirect_to_checkout" value="1">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Email <span class="required">*</span></label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror" name="email" placeholder=""
                                                    value="{{ old('email') }}" required>
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Password <span class="required">*</span></label>
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror" name="password" placeholder="" required>
                                                @error('password')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="row text-center mt-3">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-outline-dark w-50">Login</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            {{-- Register Panel --}}
                            <div class="tab-pane fade {{ (old('first_name') || old('last_name') || old('phone')) && $errors->any() ? 'show active' : '' }}" id="registerPanel" role="tabpanel">
                                <div class="checkout-card">
                                    <h5 class="checkout-title">Create New Account</h5>
                                    <form id="registerForm" method="POST" action="{{ route('register') }}">
                                        @csrf
                                        <input type="hidden" name="redirect_to_checkout" value="1">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">First Name <span
                                                        class="required">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('first_name') is-invalid @enderror" name="first_name" placeholder=""
                                                    value="{{ old('first_name') }}" required>
                                                @error('first_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Last Name <span
                                                        class="required">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('last_name') is-invalid @enderror" name="last_name" placeholder=""
                                                    value="{{ old('last_name') }}" required>
                                                @error('last_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email <span class="required">*</span></label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror" name="email" placeholder=""
                                                    value="{{ old('email') }}" required>
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone <span class="required">*</span></label>
                                                <input type="tel"
                                                    class="form-control @error('phone') is-invalid @enderror" name="phone" placeholder=""
                                                    value="{{ old('phone') }}" required>
                                                @error('phone')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Password <span class="required">*</span></label>
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror" name="password" placeholder="" required>
                                                @error('password')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Confirm Password <span
                                                        class="required">*</span></label>
                                                <input type="password" class="form-control"
                                                    name="password_confirmation" placeholder="" required>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input @error('terms') is-invalid @enderror"
                                                        type="checkbox" id="regTerms" name="terms" value="1"
                                                        {{ old('terms') ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="regTerms">
                                                        I agree to <a href="{{ route('terms-and-conditions') }}" style="color: #ff8a00; text-decoration: none;">the terms and conditions</a>
                                                    </label>
                                                    @error('terms')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row text-center mt-3">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-dark w-50">Create Account</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Authenticated User Panel --}}
                        <div class="checkout-card">
                            <h5 class="checkout-title">Customer Information</h5>
                            <form id="authUserForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="authFirstName"
                                            value="{{ auth()->user()->first_name ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="authLastName"
                                            value="{{ auth()->user()->last_name ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email <span class="required">*</span></label>
                                        <input type="email" class="form-control" id="authEmail"
                                            value="{{ auth()->user()->email ?? '' }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone <span class="required">*</span></label>
                                        <input type="tel" class="form-control" id="authPhone"
                                            value="{{ auth()->user()->phone ?? '' }}" required>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    <div class="checkout-card" id="addressCard">
                        <h5 class="checkout-title">
                            Delivery Address
                            @auth
                            <small style="font-size:11px; color:#999;">
                                (may be different from your profile address)
                            </small>
                            @endauth
                        </h5>
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="form-label">Postcode <span class="required">*</span></label>
                                <input type="text" class="form-control" id="postcodeInput" name="postcode"
                                    placeholder="e.g. LN5 8LQ">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-outline-dark w-100" id="findAddressBtn" style="height: 45px;">
                                    <i class="fas fa-search"></i> Find
                                </button>
                            </div>
                        </div>

                        <div id="addressFieldsDiv" style="display: none;">
                            <div class="row g-3 mb-3">
                                <div class="col-12">
                                    <label class="form-label">Select Address <span class="required">*</span></label>
                                    <select class="form-control" id="addressSelect">
                                        <option value="">Choose from suggestions...</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Street <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="address" name="address">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">City <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="city" name="city">
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Address Line 2 (Optional)</label>
                                    <input type="text" class="form-control" id="address2" placeholder="" name="address2">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="checkout-card">
                        <h5 class="checkout-title">Order Notes</h5>
                        <form id="notesForm">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Special Instructions (Optional)</label>
                                    <textarea class="form-control" id="orderNotes" rows="3" name="orderNotes"
                                        placeholder="Add any special requests or delivery instructions here..."></textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="checkout-card">
                                <h5 class="checkout-title">Delivery Details</h5>
                                <div id="deliveryDetailsDisplay">
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
                                        <span class="detail-label">Charge:</span>
                                        <span class="detail-value" id="deliveryChargeDisplay">£0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="checkout-card">
                                <h5 class="checkout-title">Payment Method</h5>
                                <form id="paymentForm">
                                    <div class="payment-options">
                                        <div class="payment-option" id="paymentCashOption">
                                            <input type="radio" name="paymentMethod" id="paymentCash" value="cash" class="form-check-input">
                                            <label for="paymentCash" class="payment-option-label">
                                                <i class="fas fa-money-bill-wave"></i>
                                                <div class="payment-option-text">
                                                    <strong>Cash on Delivery</strong>
                                                    <small>Pay when your order arrives</small>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="payment-option" id="paymentStripeOption">
                                            <input type="radio" name="paymentMethod" id="paymentStripe" value="stripe" class="form-check-input">
                                            <label for="paymentStripe" class="payment-option-label">
                                                <i class="fab fa-stripe"></i>
                                                <div class="payment-option-text">
                                                    <strong>Stripe</strong>
                                                    <small>Secure card payment</small>
                                                </div>
                                            </label>
                                        </div>

                                        <div class="payment-option" id="paymentPaypalOption">
                                            <input type="radio" name="paymentMethod" id="paymentPaypal" value="paypal" class="form-check-input">
                                            <label for="paymentPaypal" class="payment-option-label">
                                                <i class="fab fa-paypal"></i>
                                                <div class="payment-option-text">
                                                    <strong>PayPal</strong>
                                                    <small>Fast and secure checkout</small>
                                                </div>
                                            </label>
                                        </div>
                                    </div>
                                </form>
                                <div id="paymentErrorMsg" class="invalid-feedback mt-2 d-none">
                                    Please choose a payment method
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="order-summary-card">
                        <h5 class="checkout-title">Order Summary</h5>
                        <div class="checkout-cart-body" id="summaryItemsContainer">
                        </div>

                        <div class="summary-divider"></div>

                        <div class="promo-section-compact">
                            <label class="form-label">Promo Code / Gift Card</label>
                            <div class="row g-2">
                                <div class="col-8">
                                    <input type="text" class="form-control promo-input" id="promoCode" placeholder="Enter code">
                                </div>
                                <div class="col-4">
                                    <button class="btn btn-promo w-100" type="button" id="applyPromoBtn">Apply</button>
                                </div>
                            </div>
                            <div id="promoMessageContainer" style="display: none; margin-top: 8px;">
                                <div class="alert alert-info mb-0" id="promoMessage"></div>
                            </div>
                        </div>

                        <div class="summary-divider"></div>

                        @if(auth()->check())
                            @php
                                $userAvailablePoints = auth()->user()->available_points ?? 0;
                            @endphp
                            
                            @if($userAvailablePoints >= 100)
                                <div class="points-redemption-section">
                                    <h6 class="points-section-title">
                                        <i class="fas fa-star"></i> Redeem Points
                                    </h6>

                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input form-check-input" id="usePoints" name="use_points">
                                        <label class="custom-control-label" for="usePoints">
                                            Use my points <strong>({{ $userAvailablePoints }} available)</strong>
                                        </label>
                                    </div>

                                    <div id="pointsContainer" class="points-input-container" style="display: none;">
                                        <div class="points-info-box">
                                            <p><strong>100 points = £1</strong></p>
                                        </div>

                                        <label class="form-label">Points to Redeem</label>
                                        <input type="number" class="form-control" id="pointsToUse" name="points_to_use"
                                               min="0" value="0" placeholder="0">

                                        <div class="points-display-box">
                                            <div class="points-row">
                                                <span>Points used:</span>
                                                <strong id="pointsUsedDisplay">0</strong>
                                            </div>
                                            <div class="points-row">
                                                <span>Discount value:</span>
                                                <strong id="pointsDiscountDisplay">£0.00</strong>
                                            </div>
                                            <div class="points-row">
                                                <span>Remaining:</span>
                                                <strong id="remainingPointsDisplay">{{ $userAvailablePoints }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="summary-divider"></div>
                            @endif
                        @endif

                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span id="summarySubtotal">£0.00</span>
                        </div>
                        <div class="summary-row">
                            <span>Delivery Charge</span>
                            <span id="summaryDeliveryCharge">£0.00</span>
                        </div>
                        <div class="summary-row" id="discountRow" style="display: none;">
                            <span>Promo Discount</span>
                            <span id="summaryDiscount" style="color: #28a745;">-£0.00</span>
                        </div>
                        <div class="summary-row" id="pointsDiscountRow" style="display: none;">
                            <span>Points Discount</span>
                            <span id="summaryPointsDiscount" style="color: #28a745;">-£0.00</span>
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
            let foundAddresses = [];
            let isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
            const userAvailablePoints = {{ auth()->check() ? auth()->user()->available_points ?? 0 : 0 }};
            
            let appliedPromoCode = { type: null, id: null, discount: 0 };
            let pointsUsedDiscount = 0;
            let pointsUsed = 0;
            let selectedPaymentMethod = null;

            if (!checkoutData) {
                showError('No cart data found. Redirecting to cart...');
                window.location.href = '/';
                return;
            }

            const fieldMap = {
                'localOrder.customer.firstName': isAuthenticated ? '#authFirstName' : '#guestFirstName',
                'localOrder.customer.lastName': isAuthenticated ? '#authLastName' : '#guestLastName',
                'localOrder.customer.email': isAuthenticated ? '#authEmail' : '#guestEmail',
                'localOrder.customer.phone': isAuthenticated ? '#authPhone' : '#guestPhone',
                'localOrder.address': '#address',
                'localOrder.city': '#city',
                'localOrder.postalCode': '#postcodeInput',
                'localOrder.paymentMethod': 'input[name="paymentMethod"]'
            };

            function clearAllErrors() {
                $('.invalid-feedback:not(#paymentErrorMsg)').remove();
                $('input, select, textarea').removeClass('is-invalid');
                $('.checkout-card').removeClass('is-invalid');
                $('.payment-option').removeClass('is-invalid').css({
                    'border': '',
                    'border-radius': '',
                    'padding': '',
                    'background-color': ''
                });
                $('.payment-option label').css('color', '');
                $('#paymentErrorMsg').addClass('d-none');
            }

            function showFieldError(field, message) {
                const selector = fieldMap[field];
                
                if (!selector) {
                    showError(message);
                    return;
                }

                const $field = $(selector);
                
                if ($field.length === 0) {
                    showError(message);
                    return;
                }
                
                $field.addClass('is-invalid');
                
                if (field === 'localOrder.paymentMethod') {
                    $('.payment-option').addClass('is-invalid').css({
                        'border': '2px solid #dc3545',
                        'border-radius': '8px',
                        'padding': '10px',
                        'background-color': '#fff5f5'
                    });
                    $('.payment-option label').css('color', '#dc3545');
                    $('#paymentErrorMsg').removeClass('d-none').addClass('d-block');
                } else {
                    $field.next('.invalid-feedback').remove();
                    $field.after(`<div class="invalid-feedback d-block">${message}</div>`);
                }
            }

            function toggleAddressCard() {
                if (checkoutData.delivery.type === 'delivery') {
                    $('#addressCard').show();
                } else {
                    $('#addressCard').hide();
                    $('#postcodeInput, #address, #city, #address2').val('');
                }
            }

            if (checkoutData.delivery.postcode && checkoutData.delivery.type === 'delivery') {
                $('#postcodeInput').val(checkoutData.delivery.postcode);
                loadAddressesFromPostcode(checkoutData.delivery.postcode);
            }

            $('#findAddressBtn').on('click', function(e) {
                e.preventDefault();
                clearAllErrors();
                
                let postcode = $('#postcodeInput').val().trim().toUpperCase();
                if (!postcode) {
                    showFieldError('localOrder.postalCode', 'Postcode is required');
                    return;
                }

                validatePostcodeAndLoadAddresses(postcode);
            });

            function validatePostcodeAndLoadAddresses(postcode) {
                $('#findAddressBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Searching...');

                $.ajax({
                    url: 'https://api.postcodes.io/postcodes/' + postcode,
                    type: 'GET',
                    success: function(res) {
                        let latitude = res.result.latitude;
                        let longitude = res.result.longitude;

                        $.ajax({
                            url: "{{ route('get-addresses') }}",
                            type: 'GET',
                            data: { postcode, latitude, longitude },
                            success: function(res) {
                                foundAddresses = res.addresses;
                                let options = '<option value="">Choose from suggestions...</option>';
                                foundAddresses.forEach((addr, idx) => {
                                    options += `<option value="${idx}">${addr.display}</option>`;
                                });

                                $('#addressSelect').html(options);
                                $('#addressFieldsDiv').show();
                                updateDeliveryDetailsWithNewPostcode(postcode, res.delivery_charge);
                                showSuccess('Found ' + foundAddresses.length + ' addresses!');
                                $('#findAddressBtn').prop('disabled', false).html('<i class="fas fa-search"></i> Find');
                            },
                            error: function(xhr) {
                                $('#addressFieldsDiv').hide();
                                let message = xhr.responseJSON?.message || 'Invalid postcode';
                                showFieldError('localOrder.postalCode', message);
                                $('#findAddressBtn').prop('disabled', false).html('<i class="fas fa-search"></i> Find');
                            }
                        });
                    },
                    error: function() {
                        $('#addressFieldsDiv').hide();
                        showFieldError('localOrder.postalCode', 'Invalid postcode');
                        $('#findAddressBtn').prop('disabled', false).html('<i class="fas fa-search"></i> Find');
                    }
                });
            }

            function loadAddressesFromPostcode(postcode) {
                $.ajax({
                    url: 'https://api.postcodes.io/postcodes/' + postcode,
                    type: 'GET',
                    success: function(res) {
                        let latitude = res.result.latitude;
                        let longitude = res.result.longitude;

                        $.ajax({
                            url: "{{ route('get-addresses') }}",
                            type: 'GET',
                            data: { postcode, latitude, longitude },
                            success: function(res) {
                                foundAddresses = res.addresses;
                                let options = '<option value="">Choose from suggestions...</option>';
                                foundAddresses.forEach((addr, idx) => {
                                    options += `<option value="${idx}">${addr.display}</option>`;
                                });

                                $('#addressSelect').html(options);
                                $('#addressFieldsDiv').show();
                            }
                        });
                    }
                });
            }

            $('#addressSelect').on('change', function() {
                let idx = $(this).val();
                if (idx === '') {
                    $('#address, #city').val('');
                    return;
                }

                let selected = foundAddresses[idx];
                $('#address').val(selected.street);
                $('#city').val(selected.city);
            });

            $('#usePoints').on('change', function() {
                if ($(this).is(':checked')) {
                    $('#pointsContainer').slideDown();
                } else {
                    $('#pointsContainer').slideUp();
                    $('#pointsToUse').val(0);
                    pointsUsed = 0;
                    pointsUsedDiscount = 0;
                    updateTotals();
                }
            });

            $('#pointsToUse').on('blur input', function() {
                let val = parseInt($(this).val()) || 0;
                const subtotal = checkoutData.subtotal;
                const maxDiscount = Math.floor(subtotal * 100);
                const maxPoints = Math.min(userAvailablePoints, maxDiscount);
                
                if (val > maxPoints) val = maxPoints;
                if (val < 0) val = 0;

                $(this).val(val);
                pointsUsed = val;
                pointsUsedDiscount = val / 100;
                
                $('#pointsUsedDisplay').text(val);
                $('#pointsDiscountDisplay').text('£' + (val / 100).toFixed(2));
                $('#remainingPointsDisplay').text(userAvailablePoints - val);
                updateTotals();
            });

            function updateDeliveryDetailsWithNewPostcode(postcode, charge) {
                checkoutData.delivery.postcode = postcode;
                checkoutData.delivery.charge = parseFloat(charge);
                $('#deliveryPostcodeDisplay').text(postcode);
                $('#deliveryChargeDisplay').text('£' + parseFloat(charge).toFixed(2));
                updateTotals();
            }

            $(document).on('change', 'input[name="paymentMethod"]', function() {
                selectedPaymentMethod = $(this).val();
                clearAllErrors();
            });

            $('#applyPromoBtn').on('click', function() {
                @if(!auth()->check())
                    showError('Please login to apply coupon codes');
                    return;
                @endif

                let promoCode = $('#promoCode').val().trim().toUpperCase();
                if (!promoCode) {
                    showError('Please enter a promo code');
                    return;
                }

                let totalPrice = checkoutData.subtotal + checkoutData.deliveryCharge - pointsUsedDiscount;
                
                if (totalPrice <= 0) {
                    showError('Cannot apply coupon when total is zero or negative');
                    return;
                }

                $.ajax({
                    url: '/validate-promo-code',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({ code: promoCode, subtotal: checkoutData.subtotal }),
                    success: function(res) {
                        let finalTotal = checkoutData.subtotal + checkoutData.deliveryCharge - res.discount_amount - pointsUsedDiscount;
                        
                        if (finalTotal < 0) {
                            showError('Discount cannot exceed total amount');
                            return;
                        }

                        appliedPromoCode = {
                            type: res.type,
                            id: res.code_data.id,
                            discount: res.discount_amount
                        };

                        $('#promoMessageContainer').show();
                        let message = res.type === 'gift_card' 
                            ? `✓ Gift Card applied! Balance used: £${res.discount_amount.toFixed(2)}`
                            : `✓ Coupon applied! Discount: ${res.code_data.discount_type === 'percent' ? res.code_data.discount_value + '%' : '£' + res.discount_amount.toFixed(2)}`;

                        $('#promoMessage').html(message).removeClass('alert-danger').addClass('alert-success');
                        $('#discountRow').show();
                        $('#summaryDiscount').text(`-£${res.discount_amount.toFixed(2)}`);
                        updateTotals();
                        showSuccess('Applied successfully!');
                    },
                    error: function(err) {
                        appliedPromoCode = { type: null, id: null, discount: 0 };
                        $('#discountRow').hide();
                        $('#promoMessageContainer').hide();
                        let message = err.responseJSON?.message || 'Invalid code';
                        showError(message);
                    }
                });
            });

            function getCustomerData() {
                if (isAuthenticated) {
                    return {
                        firstName: $('#authFirstName').val().trim(),
                        lastName: $('#authLastName').val().trim(),
                        email: $('#authEmail').val().trim(),
                        phone: $('#authPhone').val().trim(),
                        type: 'authenticated'
                    };
                } else {
                    return {
                        firstName: $('#guestFirstName').val().trim(),
                        lastName: $('#guestLastName').val().trim(),
                        email: $('#guestEmail').val().trim(),
                        phone: $('#guestPhone').val().trim(),
                        type: 'guest'
                    };
                }
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
                                <div><img src="${escapeHtml(item.image)}" class="cart-item-img" alt="${escapeHtml(item.title)}"></div>
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

            function updateTotals() {
                let finalTotal = checkoutData.subtotal + checkoutData.deliveryCharge - appliedPromoCode.discount - pointsUsedDiscount;
                
                $('#summarySubtotal').text('£' + checkoutData.subtotal.toFixed(2));
                $('#summaryDeliveryCharge').text('£' + checkoutData.deliveryCharge.toFixed(2));
                
                if (appliedPromoCode.discount > 0) {
                    $('#discountRow').show();
                    $('#summaryDiscount').text('-£' + appliedPromoCode.discount.toFixed(2));
                } else {
                    $('#discountRow').hide();
                }
                
                if (pointsUsedDiscount > 0) {
                    $('#pointsDiscountRow').show();
                    $('#summaryPointsDiscount').text('-£' + pointsUsedDiscount.toFixed(2));
                } else {
                    $('#pointsDiscountRow').hide();
                }
                
                $('#summaryTotal').text('£' + finalTotal.toFixed(2));
            }

            function escapeHtml(text) {
                const map = {
                    '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
                };
                return String(text).replace(/[&<>"']/g, m => map[m]);
            }

            $('#confirmOrderBtn').on('click', function() {
                clearAllErrors();

                let address = $('#address').val().trim();
                let address2 = $('#address2').val().trim();
                let city = $('#city').val().trim();
                let postalCode = $('#postcodeInput').val().trim();
                let orderNotes = $('#orderNotes').val().trim();
                let customerData = getCustomerData();

                let $btn = $('#confirmOrderBtn');
                let originalText = $btn.text();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Please wait...');

                let totalPrice = checkoutData.subtotal + checkoutData.deliveryCharge - appliedPromoCode.discount - pointsUsedDiscount;
                totalPrice = Math.round(totalPrice * 100) / 100;

                let hubRiseItems = [];
                checkoutData.cart.forEach(item => {
                    let itemPrice = Number(item.price);
                    let itemQuantity = Number(item.quantity);
                    let priceValue = itemPrice.toFixed(2);

                    let hubRiseItem = {
                        product_name: item.title,
                        sku_ref: item.skuRef || '',
                        quantity: itemQuantity,
                        price: priceValue + ' GBP'
                    };

                    if (item.type === 'custom' && item.options && Object.keys(item.options).length > 0) {
                        hubRiseItem.options = [];
                        Object.entries(item.options).forEach(([optionName, optionValues]) => {
                            optionValues.forEach(opt => {
                                hubRiseItem.options.push({
                                    option_list_name: 'Option',
                                    name: opt.title,
                                    ref: opt.hubriseOptionRef || '',
                                    price: (0).toFixed(2) + ' GBP'
                                });
                            });
                        });
                    }

                    hubRiseItems.push(hubRiseItem);
                });

                let hubRiseOrder = {
                    status: 'new',
                    channel: 'Website',
                    service_type: checkoutData.delivery.type === 'delivery' ? 'delivery' : 'collection',
                    items: hubRiseItems,
                    payments: selectedPaymentMethod === 'cash' ? [] : [{
                        type: selectedPaymentMethod,
                        name: $(`input[name="paymentMethod"][value="${selectedPaymentMethod}"]`).next().find('strong').text(),
                        amount: totalPrice.toFixed(2) + ' GBP'
                    }],
                    customer: {
                        first_name: customerData.firstName || '',
                        last_name: customerData.lastName || '',
                        email: customerData.email,
                        phone: customerData.phone || '',
                        address_1: address || '',
                        address_2: address2 || '',
                        city: city || '',
                        postal_code: postalCode || ''
                    },
                    notes: orderNotes
                };

                if (checkoutData.delivery.type === 'delivery') {
                    hubRiseOrder.charges = [{
                        name: 'Delivery',
                        price: checkoutData.deliveryCharge.toFixed(2) + ' GBP'
                    }];
                }

                if (appliedPromoCode.type === 'coupon' && appliedPromoCode.discount > 0) {
                    hubRiseOrder.discounts = [{
                        name: 'Coupon: ' + $('#promoCode').val().trim().toUpperCase(),
                        price_off: appliedPromoCode.discount.toFixed(2) + ' GBP'
                    }];
                }

                if (appliedPromoCode.type === 'gift_card' && appliedPromoCode.discount > 0) {
                    hubRiseOrder.discounts = [{
                        name: 'Gift Card',
                        price_off: appliedPromoCode.discount.toFixed(2) + ' GBP'
                    }];
                }

                let localOrder = {
                    customer: customerData,
                    address: address,
                    address2: address2,
                    city: city,
                    postalCode: postalCode,
                    orderNotes: orderNotes,
                    cart: checkoutData.cart,
                    delivery: checkoutData.delivery,
                    subtotal: checkoutData.subtotal,
                    deliveryCharge: checkoutData.deliveryCharge,
                    promo_type: appliedPromoCode.type,
                    promo_id: appliedPromoCode.id,
                    promo_discount: appliedPromoCode.discount,
                    points_used: parseInt($('#pointsToUse').val()) || 0,
                    paymentMethod: selectedPaymentMethod,
                    total: totalPrice
                };

                $.ajax({
                    url: '/place-order',
                    type: 'POST',
                    contentType: 'application/json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: JSON.stringify({
                        hubRiseOrder: hubRiseOrder,
                        localOrder: localOrder
                    }),
                    success: function(response) {
                        if (response.redirectUrl) {
                            window.location.href = response.redirectUrl;
                        } else {
                            showSuccess('Order placed successfully!');
                            localStorage.removeItem('cart');
                            localStorage.removeItem('cartSummary');
                            localStorage.removeItem('deliveryOptions');
                            localStorage.removeItem('checkoutData');

                            setTimeout(() => {
                                window.location.href = '/order-confirmation/' + response.orderNumber;
                            }, 1500);
                        }
                    },
                    error: function(err) {
                        $btn.prop('disabled', false).text(originalText);
                        clearAllErrors();
                        
                        if (err.responseJSON && err.responseJSON.errors) {
                            let firstField = null;
                            
                            $.each(err.responseJSON.errors, function(field, messages) {
                                showFieldError(field, messages[0]);
                                if (!firstField && fieldMap[field]) {
                                    firstField = $(fieldMap[field]);
                                }
                            });

                            if (firstField && firstField.length) {
                                $('html, body').animate({ scrollTop: firstField.offset().top - 100 }, 500);
                            }
                        } else {
                            showError(err.responseJSON?.message || 'Error placing order. Please try again.');
                        }
                    }
                });
            });

            displayDeliveryDetails();
            toggleAddressCard();
            displaySummaryItems();
            updateTotals();
        });
    </script>
@endsection