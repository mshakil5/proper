@extends('frontend.master')

@section('content')
    <section class="section">
        <div class="container px-5 mx-5">
            <div class="vouchers-header-wrapper my-5">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <div class="eyebrow mb-3">Available</div>
                        <h1 class="big-title mb-0">Our <span class="accent">Gift Cards</span></h1>
                    </div>

                    <div class="col-md-6">
                        <div class="filter-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="filter-input" id="packageSearch" placeholder="Search gift cards...">
                        </div>
                    </div>
                </div>
            </div>

            <div class="vouchers-grid">
                @forelse($packages as $package)
                    <button type="button" class="gift-card-display btn-buy-card" data-package-name="{{ strtolower($package->name) }}" data-package-desc="{{ strtolower($package->description ?? '') }}" data-package-id="{{ $package->id }}" data-amount="{{ $package->amount }}" style="background: none; border: none; padding: 0; cursor: pointer; text-align: left; transition: all 0.3s ease;">
                        <div class="gift-card-visual">
                            <div class="gift-card-coupon">
                                <div class="coupon-border"></div>
                                
                                <div class="coupon-content">
                                    <div class="coupon-left">
                                        <i class="fas fa-gift"></i>
                                    </div>

                                    <div class="coupon-middle">
                                        <div class="coupon-branding">
                                            <div class="brand-logo"><img src={{ asset('uploads/company/' . $logo) }} style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;"></div>
                                            <small class="brand-url">www.propertakeaways.co.uk</small>
                                        </div>
                                        
                                        <div class="coupon-amount">
                                            £{{ number_format($package->amount) }}
                                        </div>
                                        
                                        <div class="coupon-label">
                                            GIFT CARD
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </button>
                @empty
                    <div class="col-12 text-center py-5">
                        <i class="fas fa-inbox" style="font-size: 48px; color: #ddd;"></i>
                        <p style="color: #999; margin-top: 16px;">No active gift cards</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Payment Method Modal -->
    <div id="paymentMethodModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title">Select Payment Method</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="payment-options">
                        <div class="payment-option" data-method="stripe">
                            <input type="radio" name="giftcardPaymentMethod" id="giftcardPaymentStripe" value="stripe" class="form-check-input">
                            <label for="giftcardPaymentStripe" class="payment-option-label">
                                <i class="fab fa-stripe"></i>
                                <div class="payment-option-text">
                                    <strong>Stripe</strong>
                                    <small>Secure card payment</small>
                                </div>
                            </label>
                        </div>

                        <div class="payment-option" data-method="paypal">
                            <input type="radio" name="giftcardPaymentMethod" id="giftcardPaymentPaypal" value="paypal" class="form-check-input">
                            <label for="giftcardPaymentPaypal" class="payment-option-label">
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
                    <button type="button" class="btn btn-gradient" id="confirmPaymentMethodBtn">Confirm & Pay</button>
                </div>
            </div>
        </div>
    </div>

    <div id="loadingModal" style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); display: none; align-items: center; justify-content: center; z-index: 9999;">
        <div style="background: white; padding: 40px; border-radius: 16px; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
            <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #ff8a00; margin-bottom: 16px; display: block;"></i>
            <h4 style="margin: 0 0 8px; color: #1a1a1a;">Processing Payment</h4>
            <p style="margin: 0; color: #999; font-size: 14px;">Please wait, redirecting you to payment...</p>
        </div>
    </div>
@endsection

@section('script')
<script>
$(function() {
    let selectedPackageId = null;
    let selectedAmount = null;
    let selectedPaymentMethod = null;

    @if(session('success'))
        showSuccess('{{ session("success") }}');
    @endif

    @if(session('error'))
        showError('{{ session("error") }}');
    @endif

    $(document).on('click', '.btn-buy-card', function() {
        selectedPackageId = $(this).data('package-id');
        selectedAmount = $(this).data('amount');
        
        @if(auth()->check())
            $('#giftcardPaymentStripe').prop('checked', true);
            selectedPaymentMethod = 'stripe';
            $('#paymentMethodModal').modal('show');
        @else
            showError('Please login to purchase gift cards');
        @endif
    });

    $('#confirmPaymentMethodBtn').on('click', function() {
        selectedPaymentMethod = $('input[name="giftcardPaymentMethod"]:checked').val();
        
        if (!selectedPaymentMethod) {
            showError('Please select a payment method');
            return;
        }

        $('#paymentMethodModal').modal('hide');
        $('#loadingModal').css('display', 'flex');
        initiateGiftCardPayment(selectedPackageId, selectedAmount, selectedPaymentMethod);
    });

    window.initiateGiftCardPayment = function(packageId, amount, paymentMethod) {
        $.ajax({
            url: '{{ route("giftcard.checkout") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                package_id: packageId,
                amount: amount,
                payment_method: paymentMethod
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirectUrl;
                } else {
                    showError(response.message);
                    $('#loadingModal').css('display', 'none');
                }
            },
            error: function(xhr) {
                showError(xhr.responseJSON?.message ?? 'Failed to process payment');
                $('#loadingModal').css('display', 'none');
            }
        });
    };

    $('#packageSearch').on('keyup', function() {
        let searchValue = $(this).val().toLowerCase();
        
        $('.gift-card-display').each(function() {
            let name = $(this).data('package-name');
            let desc = $(this).data('package-desc');
            
            if (name.includes(searchValue) || desc.includes(searchValue)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});
</script>
@endsection