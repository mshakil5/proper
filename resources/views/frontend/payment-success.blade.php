@extends('frontend.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-success">
                <div class="card-body text-center">
                    <h1 class="text-success mb-3">
                        <i class="fas fa-check-circle"></i> Payment Successful!
                    </h1>
                    
                    <p class="text-muted mb-3">Your order has been placed successfully</p>
                    
                    <div class="alert alert-info mb-3">
                        <strong>Order Number:</strong> {{ $orderNumber }}
                    </div>

                    <p class="mb-4">Thank you for your order. You will receive a confirmation email shortly.</p>

                    <a href="{{ route('order.confirmation', ['orderNumber' => $orderNumber]) }}" class="btn btn-success btn-lg">
                        View Order Details
                    </a>
                    <a href="/" class="btn btn-outline-success btn-lg">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Clear localStorage after payment success
    localStorage.removeItem('cart');
    localStorage.removeItem('cartSummary');
    localStorage.removeItem('deliveryOptions');
    localStorage.removeItem('checkoutData');
    
    console.log('Payment successful - localStorage cleared');
</script>
@endsection