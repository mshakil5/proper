@extends('frontend.master')

@section('content')
<div class="checkout-wrapper">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="order-summary-card">
                    <div style="text-align: center; padding: 20px 0;">
                        <i class="fas fa-check-circle" style="font-size: 56px; color: #28a745; margin-bottom: 16px; display: block;"></i>
                        <h2 style="color: #28a745; margin-bottom: 12px;">Payment Successful!</h2>
                        <p style="color: #666; margin: 0;">Your order has been placed successfully</p>
                    </div>

                    <div class="summary-divider"></div>

                    <div class="alert alert-success mb-0" style="background: linear-gradient(135deg, #d4edda, #c3e6cb); border-left: 4px solid #28a745; color: #155724;">
                        <strong>Order Number:</strong> <span style="font-family: monospace; letter-spacing: 1px;">{{ $orderNumber }}</span>
                    </div>

                    <p style="color: #666; font-size: 13px; margin: 20px 0 0; text-align: center;">Thank you for your order. You will receive a confirmation email shortly.</p>

                    <div style="display: flex; flex-direction: column; gap: 12px; margin-top: 24px;">
                        <a href="{{ route('order.confirmation', ['orderNumber' => $orderNumber]) }}" class="btn-place-order" style="background: linear-gradient(135deg, #28a745, #20c997); margin: 0; text-decoration: none;">
                            <i class="fas fa-file-alt"></i> View Order Details
                        </a>
                        <a href="/" class="btn-place-order" style="background: white; color: #ff8a00; border: 2px solid #ff8a00; margin: 0; text-decoration: none;">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    localStorage.removeItem('cart');
    localStorage.removeItem('cartSummary');
    localStorage.removeItem('deliveryOptions');
    localStorage.removeItem('checkoutData');
</script>
@endsection