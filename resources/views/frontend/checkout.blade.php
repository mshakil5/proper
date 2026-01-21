@extends('frontend.master')

@section('content')
    <div class="checkout-wrapper">
        <div class="container py-4">
            <div class="row g-4">
                <!-- Left Column: Customer & Delivery Details -->
                <div class="col-lg-8">
                    @if (!auth()->check())
                        <!-- Customer Type Selection Tabs (Only for guests) -->
                        <div class="checkout-card">
                            <h5 class="checkout-title">Checkout As</h5>
                            <div class="nav nav-tabs" role="tablist" id="customerTypeTabs">
                                <button class="nav-link active" id="guest-tab" data-bs-toggle="tab"
                                    data-bs-target="#guestPanel" type="button" role="tab">
                                    <i class="fas fa-user-secret"></i> Guest
                                </button>
                                <button class="nav-link" id="existing-tab" data-bs-toggle="tab"
                                    data-bs-target="#existingPanel" type="button" role="tab">
                                    <i class="fas fa-sign-in-alt"></i> Login
                                </button>
                                <button class="nav-link" id="register-tab" data-bs-toggle="tab"
                                    data-bs-target="#registerPanel" type="button" role="tab">
                                    <i class="fas fa-user-plus"></i> Register
                                </button>
                            </div>
                        </div>

                        <!-- Tab Panels -->
                        <div class="tab-content" id="customerTypeContent">
                            <!-- Guest Checkout Panel -->
                            <div class="tab-pane fade show active" id="guestPanel" role="tabpanel">
                                <div class="checkout-card">
                                    <h5 class="checkout-title">Guest Details</h5>
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle"></i> Checkout as guest - you may lose loyalty points
                                        and exclusive features.
                                    </div>
                                    <form id="guestForm">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">First Name <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="guestFirstName"
                                                    placeholder="John" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Last Name <span class="required">*</span></label>
                                                <input type="text" class="form-control" id="guestLastName"
                                                    placeholder="Doe" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email <span class="required">*</span></label>
                                                <input type="email" class="form-control" id="guestEmail"
                                                    placeholder="john@example.com" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone <span class="required">*</span></label>
                                                <input type="tel" class="form-control" id="guestPhone"
                                                    placeholder="+44 123 456 7890" required>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Existing Customer Panel -->
                            <div class="tab-pane fade" id="existingPanel" role="tabpanel">
                                <div class="checkout-card">
                                    <h5 class="checkout-title">Login to Your Account</h5>
                                    <form id="loginForm" method="POST" action="{{ route('login') }}">
                                        @csrf
                                        <input type="hidden" name="redirect_to_checkout" value="1">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Email <span class="required">*</span></label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    id="loginEmail" name="email" placeholder="your@email.com"
                                                    value="{{ old('email') }}">
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Password <span class="required">*</span></label>
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="loginPassword" name="password" placeholder="Enter password">
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

                            <!-- Register Panel -->
                            <div class="tab-pane fade" id="registerPanel" role="tabpanel">
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
                                                    class="form-control @error('first_name') is-invalid @enderror"
                                                    id="regFirstName" name="first_name" placeholder="John"
                                                    value="{{ old('first_name') }}">
                                                @error('first_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Last Name <span
                                                        class="required">*</span></label>
                                                <input type="text"
                                                    class="form-control @error('last_name') is-invalid @enderror"
                                                    id="regLastName" name="last_name" placeholder="Doe"
                                                    value="{{ old('last_name') }}">
                                                @error('last_name')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Email <span class="required">*</span></label>
                                                <input type="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    id="regEmail" name="email" placeholder="john@example.com"
                                                    value="{{ old('email') }}">
                                                @error('email')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Phone <span class="required">*</span></label>
                                                <input type="tel"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    id="regPhone" name="phone" placeholder="+44 123 456 7890"
                                                    value="{{ old('phone') }}">
                                                @error('phone')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Password <span class="required">*</span></label>
                                                <input type="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    id="regPassword" name="password" placeholder="Create a password">
                                                @error('password')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Confirm Password <span
                                                        class="required">*</span></label>
                                                <input type="password" class="form-control" id="regConfirmPassword"
                                                    name="password_confirmation" placeholder="Confirm password">
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input @error('terms') is-invalid @enderror"
                                                        type="checkbox" id="regTerms" name="terms" value="1"
                                                        {{ old('terms') ? 'checked' : '' }} required>
                                                    <label class="form-check-label" for="regTerms">
                                                        I agree to the terms and conditions
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
                        <!-- Authenticated User Details Card -->
                        <div class="checkout-card">
                            <h5 class="checkout-title">Customer Information</h5>
                            <form id="authUserForm">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">First Name <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="authFirstName"
                                            value="{{ auth()->user()->first_name ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Last Name <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="authLastName"
                                            value="{{ auth()->user()->last_name ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Email <span class="required">*</span></label>
                                        <input type="email" class="form-control" id="authEmail"
                                            value="{{ auth()->user()->email ?? '' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone <span class="required">*</span></label>
                                        <input type="tel" class="form-control" id="authPhone"
                                            value="{{ auth()->user()->phone ?? '' }}">
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- Delivery Address Card (show for delivery only) -->
                    <div class="checkout-card" id="addressCard" style="display: none;">
                        <h5 class="checkout-title" id="addressCardTitle">Delivery Address</h5>
                            <small style="font-size:12px; color:#999">
                                (may be different from your profile address)
                            </small>
                        <form id="addressForm">
                            <!-- Postcode Row -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label">Postcode <span class="required">*</span></label>
                                    <input type="text" class="form-control" id="postcodeInput"
                                        placeholder="e.g. MK44NP">
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="button" class="btn btn-outline-dark w-100" id="findAddressBtn" style="height: 45px;">
                                        <i class="fas fa-search"></i> Find
                                    </button>
                                </div>
                            </div>

                            <!-- Address Selection & Fields (Combined) -->
                            <div id="addressFieldsDiv" style="display: none;">
                                <!-- Select Address -->
                                <div class="row g-3 mb-3">
                                    <div class="col-12">
                                        <label class="form-label">Select Address <span class="required">*</span></label>
                                        <select class="form-control" id="addressSelect">
                                            <option value="">Choose from suggestions...</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Street & City (col-6 each) -->
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Street <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="address">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">City <span class="required">*</span></label>
                                        <input type="text" class="form-control" id="city">
                                    </div>
                                </div>

                                <!-- Address Line 2 -->
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">Address Line 2 (Optional)</label>
                                        <input type="text" class="form-control" id="address2"
                                            placeholder="Apt, Suite, etc.">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Order Notes Card -->
                    <div class="checkout-card">
                        <h5 class="checkout-title">Order Notes</h5>
                        <form id="notesForm">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label">Special Instructions (Optional)</label>
                                    <textarea class="form-control" id="orderNotes" rows="3"
                                        placeholder="Add any special requests or delivery instructions here..."></textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Bottom Row: 2 Columns -->
                    <div class="row g-4">
                        <!-- Delivery Details -->
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

                        <!-- Payment Method -->
                        <div class="col-md-6">
                            <div class="checkout-card">
                                <h5 class="checkout-title">Payment</h5>
                                <button class="btn btn-outline-dark w-100" type="button" data-bs-toggle="offcanvas"
                                    data-bs-target="#paymentOffcanvas">
                                    <i class="fas fa-credit-card"></i> Select Payment Method
                                </button>
                                <div id="selectedPaymentDisplay" style="margin-top: 12px; display: none;">
                                    <div class="payment-badge">
                                        <i class="fas fa-check-circle" style="color: #28a745;"></i>
                                        <div class="payment-info">
                                            <strong id="paymentMethodName">-</strong>
                                            <small id="paymentMethodDesc">-</small>
                                        </div>
                                    </div>
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

                        <!-- Promo Code Section -->
                        <div class="promo-section-compact">
                            <label class="form-label">Promo Code / Gift Card</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="promoCode" placeholder="Enter code">
                                <button class="btn btn-outline-dark" type="button" id="applyPromoBtn">Apply</button>
                            </div>
                            <div id="promoMessageContainer" style="display: none; margin-top: 8px;">
                                <div class="alert alert-info mb-0" id="promoMessage"></div>
                            </div>
                        </div>

                        <div class="summary-divider"></div>

                        <!-- Points Redemption (Only for authenticated users with 100+ points) -->
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

                        <!-- Pricing -->
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

    <!-- Payment Method Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="paymentOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Select Payment Method</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body">
            <div class="payment-options">
                <div class="payment-option" data-method="cash">
                    <input type="radio" name="paymentMethod" id="paymentCash" value="cash"
                        class="form-check-input">
                    <label for="paymentCash" class="payment-option-label">
                        <i class="fas fa-money-bill-wave"></i>
                        <div class="payment-option-text">
                            <strong>Cash on Delivery</strong>
                            <small>Pay when your order arrives</small>
                        </div>
                    </label>
                </div>

                <div class="payment-option" data-method="stripe">
                    <input type="radio" name="paymentMethod" id="paymentStripe" value="stripe"
                        class="form-check-input">
                    <label for="paymentStripe" class="payment-option-label">
                        <i class="fab fa-stripe"></i>
                        <div class="payment-option-text">
                            <strong>Stripe</strong>
                            <small>Secure card payment</small>
                        </div>
                    </label>
                </div>

                <div class="payment-option" data-method="paypal">
                    <input type="radio" name="paymentMethod" id="paymentPaypal" value="paypal"
                        class="form-check-input">
                    <label for="paymentPaypal" class="payment-option-label">
                        <i class="fab fa-paypal"></i>
                        <div class="payment-option-text">
                            <strong>PayPal</strong>
                            <small>Fast and secure checkout</small>
                        </div>
                    </label>
                </div>
            </div>

            <button type="button" class="btn btn-outline-dark w-100 mt-4" id="confirmPaymentBtn"
                data-bs-dismiss="offcanvas">Confirm Payment Method</button>
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
            
            let appliedPromoCode = {
                type: null,
                id: null,
                discount: 0
            };
            let pointsUsedDiscount = 0;
            let pointsUsed = 0;
            let currentTab = 'guest';
            let selectedPaymentMethod = 'cash';

            if (!checkoutData) {
                showError('No cart data found. Redirecting to cart...');
                window.location.href = '/';
                return;
            }

            function toggleAddressCard() {
                if (checkoutData.delivery.type === 'delivery') {
                    $('#addressCard').show();
                    $('#addressCardTitle').text('Delivery Address');
                    $('#postcodeInput').prop('required', true);
                } else {
                    $('#addressCard').hide();
                    $('#postcodeInput').prop('required', false);
                    $('#address').val('');
                    $('#city').val('');
                    $('#address2').val('');
                    $('#postcodeInput').val('');
                }
            }

            if (checkoutData.delivery.postcode && checkoutData.delivery.type === 'delivery') {
                $('#postcodeInput').val(checkoutData.delivery.postcode);
                loadAddressesFromPostcode(checkoutData.delivery.postcode);
            }

            $('#findAddressBtn').on('click', function(e) {
                e.preventDefault();

                let postcode = $('#postcodeInput').val().trim().toUpperCase();

                if (!postcode) {
                    showError('Please enter postcode');
                    return;
                }

                validatePostcodeAndLoadAddresses(postcode);
            });

            function validatePostcodeAndLoadAddresses(postcode) {
                $('#findAddressBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Searching...');

                $.ajax({
                    url: 'https://api.postcodes.io/postcodes/' + postcode,
                    type: 'GET',
                    success: function(res) {
                        let latitude = res.result.latitude;
                        let longitude = res.result.longitude;

                        $.ajax({
                            url: "{{ route('get-addresses') }}",
                            type: 'GET',
                            data: {
                                postcode: postcode,
                                latitude: latitude,
                                longitude: longitude
                            },
                            success: function(res) {
                                foundAddresses = res.addresses;

                                let options =
                                    '<option value="">Choose from suggestions...</option>';
                                foundAddresses.forEach((addr, idx) => {
                                    options +=
                                        `<option value="${idx}">${addr.display}</option>`;
                                });

                                $('#addressSelect').html(options);
                                $('#addressFieldsDiv').show();

                                updateDeliveryDetailsWithNewPostcode(postcode, res
                                    .delivery_charge);

                                showSuccess('Found ' + foundAddresses.length +
                                    ' addresses!');
                                $('#findAddressBtn').prop('disabled', false).html(
                                    '<i class="fas fa-search"></i> Find');
                            },
                            error: function(xhr) {
                                $('#addressFieldsDiv').hide();
                                let message = xhr.responseJSON?.message ||
                                    'Invalid postcode';
                                showError(message);
                                $('#findAddressBtn').prop('disabled', false).html(
                                    '<i class="fas fa-search"></i> Find');
                            }
                        });
                    },
                    error: function() {
                        $('#addressFieldsDiv').hide();
                        showError('Invalid postcode');
                        $('#findAddressBtn').prop('disabled', false).html(
                            '<i class="fas fa-search"></i> Find');
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
                            data: {
                                postcode: postcode,
                                latitude: latitude,
                                longitude: longitude
                            },
                            success: function(res) {
                                foundAddresses = res.addresses;

                                let options =
                                    '<option value="">Choose from suggestions...</option>';
                                foundAddresses.forEach((addr, idx) => {
                                    options +=
                                        `<option value="${idx}">${addr.display}</option>`;
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
                    $('#address').val('');
                    $('#city').val('');
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

            $('#pointsToUse').on('blur', function() {
                let val = parseInt($(this).val()) || 0;
                const subtotal = checkoutData.subtotal;
                const maxDiscount = Math.floor(subtotal * 100);
                const maxPoints = Math.min(userAvailablePoints, maxDiscount);
                
                if (val > maxPoints) {
                    val = maxPoints;
                }
                
                if (val < 0) {
                    val = 0;
                }

                $(this).val(val);
                pointsUsed = val;
                pointsUsedDiscount = val / 100;
                
                $('#pointsUsedDisplay').text(pointsUsed);
                $('#pointsDiscountDisplay').text('£' + pointsUsedDiscount.toFixed(2));
                $('#remainingPointsDisplay').text(userAvailablePoints - pointsUsed);
                
                updateTotals();
            }).on('input', function() {
                let val = parseInt($(this).val()) || 0;
                const subtotal = checkoutData.subtotal;
                const maxDiscount = Math.floor(subtotal * 100);
                const maxPoints = Math.min(userAvailablePoints, maxDiscount);
                
                if (val > maxPoints) {
                    $(this).val(maxPoints);
                    val = maxPoints;
                }
                
                if (val >= 0) {
                    $('#pointsUsedDisplay').text(val);
                    $('#pointsDiscountDisplay').text('£' + (val / 100).toFixed(2));
                    $('#remainingPointsDisplay').text(userAvailablePoints - val);
                }
            });

            function updateDeliveryDetailsWithNewPostcode(postcode, charge) {
                checkoutData.delivery.postcode = postcode;
                checkoutData.delivery.charge = parseFloat(charge);

                $('#deliveryPostcodeDisplay').text(postcode);
                $('#deliveryChargeDisplay').text('£' + parseFloat(charge).toFixed(2));

                updateTotals();
            }

            $('#paymentCash').prop('checked', true);
            $('#paymentMethodName').text('Cash on Delivery');
            $('#paymentMethodDesc').text('Pay when your order arrives');
            $('#selectedPaymentDisplay').show();

            $(document).on('change', 'input[name="paymentMethod"]', function() {
                selectedPaymentMethod = $(this).val();
            });

            $('#confirmPaymentBtn').on('click', function() {
                let selectedMethod = $('input[name="paymentMethod"]:checked').val();
                if (!selectedMethod) {
                    showError('Please select a payment method');
                    return;
                }

                selectedPaymentMethod = selectedMethod;

                let methodNames = {
                    'cash': {
                        name: 'Cash on Delivery',
                        desc: 'Pay when your order arrives'
                    },
                    'stripe': {
                        name: 'Stripe',
                        desc: 'Secure card payment'
                    },
                    'paypal': {
                        name: 'PayPal',
                        desc: 'Fast and secure checkout'
                    }
                };

                let method = methodNames[selectedMethod];
                $('#paymentMethodName').text(method.name);
                $('#paymentMethodDesc').text(method.desc);
                $('#selectedPaymentDisplay').show();
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

                $.ajax({
                    url: '/validate-promo-code',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Content-Type': 'application/json'
                    },
                    data: JSON.stringify({
                        code: promoCode,
                        subtotal: checkoutData.subtotal
                    }),
                    success: function(res) {
                        appliedPromoCode = {
                            type: res.type,
                            id: res.code_data.id,
                            discount: res.discount_amount
                        };

                        $('#promoMessageContainer').show();
                        let message = res.type === 'gift_card' 
                            ? `✓ Gift Card applied! Balance used: £${res.discount_amount.toFixed(2)}`
                            : `✓ Coupon applied! Discount: ${res.code_data.discount_type === 'percent' ? res.code_data.discount_value + '%' : '£' + res.discount_amount.toFixed(2)}`;

                        $('#promoMessage').html(message)
                            .removeClass('alert-danger').addClass('alert-success');
                        $('#discountRow').show();
                        $('#summaryDiscount').text(`-£${res.discount_amount.toFixed(2)}`);

                        updateTotals();
                        showSuccess(res.type === 'gift_card' ? 'Gift card applied!' : 'Coupon applied!');
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
                let customerData = {};

                if (isAuthenticated) {
                    customerData = {
                        firstName: '{{ auth()->check() ? auth()->user()->first_name : '' }}',
                        lastName: '{{ auth()->check() ? auth()->user()->last_name : '' }}',
                        email: '{{ auth()->check() ? auth()->user()->email : '' }}',
                        phone: '{{ auth()->check() ? auth()->user()->phone : '' }}',
                        type: 'authenticated'
                    };
                } else if (currentTab === 'guest') {
                    customerData = {
                        firstName: $('#guestFirstName').val().trim(),
                        lastName: $('#guestLastName').val().trim(),
                        email: $('#guestEmail').val().trim(),
                        phone: $('#guestPhone').val().trim(),
                        type: 'guest'
                    };
                } else if (currentTab === 'existing') {
                    customerData = {
                        email: $('#loginEmail').val().trim(),
                        password: $('#loginPassword').val(),
                        type: 'login'
                    };
                } else if (currentTab === 'register') {
                    customerData = {
                        firstName: $('#regFirstName').val().trim(),
                        lastName: $('#regLastName').val().trim(),
                        email: $('#regEmail').val().trim(),
                        phone: $('#regPhone').val().trim(),
                        password: $('#regPassword').val(),
                        confirmPassword: $('#regConfirmPassword').val(),
                        type: 'register'
                    };
                }

                return customerData;
            }

            function validateCustomerData(data) {
                if (data.type === 'authenticated') {
                    return true;
                } else if (data.type === 'guest') {
                    if (!data.firstName || !data.lastName || !data.email || !data.phone) {
                        showError('Please fill in all guest details');
                        return false;
                    }
                } else if (data.type === 'login') {
                    if (!data.email || !data.password) {
                        showError('Please enter email and password');
                        return false;
                    }
                } else if (data.type === 'register') {
                    if (!data.firstName || !data.lastName || !data.email || !data.phone || !data.password || !data
                        .confirmPassword) {
                        showError('Please fill in all registration fields');
                        return false;
                    }
                    if (data.password !== data.confirmPassword) {
                        showError('Passwords do not match');
                        return false;
                    }
                }
                return true;
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
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return String(text).replace(/[&<>"']/g, m => map[m]);
            }

            $('#confirmOrderBtn').on('click', function() {
                let address = $('#address').val().trim();
                let address2 = $('#address2').val().trim();
                let city = $('#city').val().trim();
                let postalCode = $('#postcodeInput').val().trim();
                let orderNotes = $('#orderNotes').val().trim();

                if (checkoutData.delivery.type === 'delivery') {
                    if (!address || !city || !postalCode) {
                        showError('Please select delivery address');
                        return;
                    }
                }

                if (!selectedPaymentMethod) {
                    showError('Please select a payment method');
                    return;
                }

                let customerData = getCustomerData();
                if (!validateCustomerData(customerData)) {
                    return;
                }

                let $btn = $('#confirmOrderBtn');
                let originalText = $btn.text();
                $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Please wait...');

                let hubRiseItems = [];
                let totalPrice = checkoutData.subtotal + checkoutData.deliveryCharge - appliedPromoCode.discount - pointsUsedDiscount;

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
                    payments: [{
                        type: selectedPaymentMethod,
                        name: $(`input[name="paymentMethod"][value="${selectedPaymentMethod}"]`)
                            .next().find('strong').text(),
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

                // console.log(localOrder);
                // return;

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
                        if (err.responseJSON && err.responseJSON.message) {
                            showError(err.responseJSON.message);
                        } else {
                            showError('Error placing order. Please try again.');
                        }
                    }
                });
            });

            if (!isAuthenticated) {
                $('#customerTypeTabs button').on('click', function() {
                    currentTab = $(this).attr('id').replace('-tab', '');
                });
            }

            displayDeliveryDetails();
            toggleAddressCard();
            displaySummaryItems();
            updateTotals();
        });
    </script>
@endsection