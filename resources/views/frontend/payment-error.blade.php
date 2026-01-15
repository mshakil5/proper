@extends('frontend.master')

@section('content')
<div class="checkout-wrapper">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="checkout-card" style="text-align: center; background: linear-gradient(135deg, #f8d7da, #f5c6cb); border: 2px solid #dc3545; margin-bottom: 24px;">
                    <div style="padding: 32px 24px;">
                        <i class="fas fa-exclamation-circle" style="font-size: 56px; color: #dc3545; margin-bottom: 16px; display: block;"></i>
                        <h2 class="checkout-title" style="color: #721c24; margin-bottom: 8px;">Error</h2>
                        <p style="color: #721c24; margin: 0; font-size: 13px;">{{ $message ?? 'An error occurred while processing your payment' }}</p>
                    </div>
                </div>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <a href="/checkout" class="btn-place-order" style="background: linear-gradient(135deg, #dc3545, #c82333); margin: 0;">
                        <i class="fas fa-redo"></i> Back to Checkout
                    </a>
                    <a href="/" class="btn-place-order" style="background: white; color: #dc3545; border: 2px solid #dc3545; margin: 0;">
                        <i class="fas fa-home"></i> Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection