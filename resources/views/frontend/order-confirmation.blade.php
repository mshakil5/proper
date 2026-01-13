@extends('frontend.master')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Order Success Header -->
            <div class="card border-success mb-4">
                <div class="card-body text-center">
                    <h1 class="text-success mb-3">
                        <i class="fas fa-check-circle fa-3x"></i>
                    </h1>
                    <h2 class="mb-2">Order Confirmed!</h2>
                    <p class="text-muted">Thank you for your order. We've received it and will start preparing it soon.</p>
                </div>
            </div>

            <!-- Order Details -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">Order Number</small>
                            <p class="fw-bold">{{ $order->order_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Order Date</small>
                            <p class="fw-bold">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">Delivery Type</small>
                            <p class="fw-bold">
                                @if($order->delivery_type === 'delivery')
                                    <i class="fas fa-truck"></i> Home Delivery
                                @else
                                    <i class="fas fa-store"></i> Collection
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Delivery Time</small>
                            <p class="fw-bold">{{ $order->time }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Payment Method</small>
                            <p class="fw-bold text-capitalize">{{ ucfirst($order->payment_method) }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Payment Status</small>
                            <p class="fw-bold">
                                @if($order->payment_status === 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($order->payment_status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Details -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Customer Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">Name</small>
                            <p class="fw-bold">{{ $order->first_name }} {{ $order->last_name }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Email</small>
                            <p class="fw-bold">{{ $order->email }}</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">Phone</small>
                            <p class="fw-bold">{{ $order->phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Address</small>
                            <p class="fw-bold">
                                {{ $order->address_1 }}
                                @if($order->address_2)
                                    <br>{{ $order->address_2 }}
                                @endif
                                <br>{{ $order->city }}, {{ $order->postcode }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->product_name }}</strong>
                                        @if($item->options->count() > 0)
                                            <br>
                                            <small class="text-muted">
                                                @foreach($item->options as $option)
                                                    {{ $option->option_name }}@if(!$loop->last), @endif
                                                @endforeach
                                            </small>
                                        @endif
                                    </td>
                                    <td class="text-end">£{{ number_format($item->price, 2) }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end fw-bold">£{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 ms-auto">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal</span>
                                <span>£{{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Delivery Charge</span>
                                <span>£{{ number_format($order->delivery_charge, 2) }}</span>
                            </div>
                            @if($order->discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Discount</span>
                                <span>-£{{ number_format($order->discount, 2) }}</span>
                            </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between fw-bold fs-5">
                                <span>Total</span>
                                <span>£{{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Notes -->
            @if($order->notes)
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Special Instructions</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $order->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-flex gap-2 justify-content-center">
                <a href="/" class="btn btn-primary btn-lg">
                    <i class="fas fa-home"></i> Back to Home
                </a>
                <a href="/menu" class="btn btn-outline-primary btn-lg">
                    <i class="fas fa-utensils"></i> Order Again
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<style>
    .order-confirmation {
        animation: slideIn 0.3s ease-in-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection