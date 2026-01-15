@extends('frontend.master')

@section('content')
<div class="checkout-wrapper">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <!-- Cancelled Card -->
                <div class="checkout-card" style="text-align: center; background: linear-gradient(135deg, #fff3cd, #ffeaa7); border: 2px solid #ffc107; margin-bottom: 24px;">
                    <div style="padding: 32px 24px;">
                        <i class="fas fa-times-circle" style="font-size: 56px; color: #ff8a00; margin-bottom: 16px; display: block;"></i>
                        <h2 class="checkout-title" style="color: #856404; margin-bottom: 8px;">Payment Cancelled</h2>
                        <p style="color: #856404; margin: 0; font-size: 13px;">Your payment was cancelled. You can try again anytime.</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <a href="/checkout" class="btn-place-order" style="background: linear-gradient(135deg, #ff8a00, #ff5a00); margin: 0;">
                        <i class="fas fa-redo"></i> Back to Checkout
                    </a>
                    <a href="/" class="btn-place-order" style="background: white; color: #ff8a00; border: 2px solid #ff8a00; margin: 0;">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection