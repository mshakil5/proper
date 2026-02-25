@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    {{-- Order Header --}}
    <div class="order-details-header d-flex justify-content-between flex-wrap">
        <div class="order-details-info">
            <h4>Order #{{ $order->order_number }}</h4>
            <p><strong>Order Date:</strong> {{ $order->created_at->format('F d, Y') }}</p>
            <p><strong>Delivery Type:</strong> {{ ucfirst($order->delivery_type) }}</p>
            @if($order->time)
                <p><strong>Delivery/Collection Time:</strong> {{ $order->time }}</p>
            @endif
            <p><strong>Customer:</strong> {{ $order->first_name }} {{ $order->last_name }}</p>
            <p><strong>Email:</strong> {{ $order->email ?? '-' }}</p>
            <p><strong>Phone:</strong> {{ $order->phone ?? '-' }}</p>
            @if($order->delivery_type === 'delivery')
            <p><strong>Delivery Address:</strong> 
                {{ $order->address_1 }} 
                @if($order->address_2) , {{ $order->address_2 }} @endif
                , {{ $order->city ?? '' }} 
                , {{ $order->postcode ?? '' }}
            </p>
            @endif
            @if($order->notes)
                <p><strong>Notes:</strong> {{ $order->notes }}</p>
            @endif
        </div>

        <div class="order-details-status text-end">
            <button 
                class="btn-view-order mb-2 btn-order-again"
                data-order='@json($order)'>
                Order Again
            </button>
            <br>
            <div class="status-badge">{{ ucfirst($order->status) }}</div>
            <p>Status updated on {{ $order->updated_at->format('M d, Y') }}</p>
            <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
            <p>
                <strong>Payment Status:</strong> 
                <span class="badge bg-{{ $order->payment?->status_badge ?? 'secondary' }}">
                    {{ ucfirst($order->payment?->status ?? 'pending') }}
                </span>
            </p>
        </div>
    </div>

    {{-- Order Items --}}
    <div class="order-items-list mt-4">
        <h5>Order Items</h5>
        <div class="row">
            @forelse($order->items as $item)
                <div class="col-12 col-md-6 mb-3">
                    <div class="order-item d-flex">
                        <img src="{{ $item->product->image ? asset($item->product->image) : asset('placeholder.webp') }}" 
                             alt="Product" class="order-item-img me-3">
                        <div class="order-item-content">
                            <h6 class="order-item-name">{{ $item->product_name }}</h6>

                            {{-- Options --}}
                            @if($item->options->count())
                                @foreach($item->options as $option)
                                    <p class="order-item-detail">{{ $option->option_name }}</p>
                                @endforeach
                            @endif

                            <p class="order-item-detail">Quantity: {{ $item->quantity }}</p>
                            <p class="order-item-price">£{{ number_format($item->total, 2) }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p>No items in this order.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Order Summary --}}
    <div class="order-summary mt-4">
        <h5>Order Summary</h5>

        <div class="summary-row">
            <span>Subtotal</span>
            <span>£{{ number_format($order->subtotal, 2) }}</span>
        </div>
        @if($order->coupon_discount)
        <div class="summary-row">
            <span>Coupon/Voucher Discount</span>
            <span>-£{{ number_format($order->coupon_discount, 2) }}</span>
        </div>
        @endif
        @if($order->gift_card_discount)
        <div class="summary-row">
            <span>Gift Card Discount</span>
            <span>-£{{ number_format($order->gift_card_discount, 2) }}</span>
        </div>
        @endif
        @if($order->points_used)
        <div class="summary-row">
            <span>Points Used</span>
            <span>-£{{ number_format($order->points_used, 2) }}</span>
        </div>
        @endif

        <div class="summary-row total">
            <span>Total Amount</span>
            <span>£{{ number_format($order->total, 2) }}</span>
        </div>
    </div>
</div>

<a href="{{ url()->previous() }}" class="btn-reorder-now">Back to Orders</a>

@endsection