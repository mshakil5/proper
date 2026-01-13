@extends('frontend.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-warning">
                <div class="card-body text-center">
                    <h1 class="text-warning mb-3">
                        <i class="fas fa-times-circle"></i> Payment Cancelled
                    </h1>
                    
                    <p class="text-muted mb-4">Your payment was cancelled. You can try again anytime.</p>

                    <a href="/checkout" class="btn btn-warning btn-lg">
                        Back to Checkout
                    </a>
                    <a href="/" class="btn btn-outline-warning btn-lg">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection