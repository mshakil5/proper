@extends('frontend.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card border-danger">
                <div class="card-body text-center">
                    <h1 class="text-danger mb-3">
                        <i class="fas fa-exclamation-circle"></i> Error
                    </h1>
                    
                    <p class="text-muted mb-4">{{ $message ?? 'An error occurred while processing your payment' }}</p>

                    <a href="/checkout" class="btn btn-danger btn-lg">
                        Back to Checkout
                    </a>
                    <a href="/" class="btn btn-outline-danger btn-lg">
                        Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection