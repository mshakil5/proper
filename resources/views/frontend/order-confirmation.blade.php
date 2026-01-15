@extends('frontend.master')

@section('content')
<div class="checkout-wrapper">
    <div class="container py-5">
        <div class="row g-4">
            <div class="col-lg-8">
                <!-- Success Header -->
                <div class="checkout-card" style="text-align: center; background: linear-gradient(135deg, #fff8f0, #ffe8d6); border: 2px solid rgba(255, 138, 0, 0.3); margin-bottom: 30px;">
                    <div style="padding: 30px 20px;">
                        <i class="fas fa-check-circle" style="font-size: 60px; color: #ff8a00; margin-bottom: 16px; display: block;"></i>
                        <h2 class="checkout-title" style="margin-bottom: 8px;">Order Confirmed!</h2>
                        <p style="color: #666; margin: 0;">Thank you for your order. We've received it and will start preparing it soon.</p>
                    </div>
                </div>

                <!-- Order Details Card -->
                <div class="checkout-card">
                    <h5 class="checkout-title">Order Details</h5>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                        <div>
                            <label class="form-label">Order Number</label>
                            <p style="font-size: 14px; font-weight: 700; color: #ff8a00; margin: 0;">{{ $order->order_number }}</p>
                        </div>
                        <div>
                            <label class="form-label">Order Date</label>
                            <p style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 0;">{{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <div>
                            <label class="form-label">Delivery Type</label>
                            <p style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 0;">
                                @if($order->delivery_type === 'delivery')
                                    <i class="fas fa-truck" style="color: #ff8a00;"></i> Home Delivery
                                @else
                                    <i class="fas fa-store" style="color: #ff8a00;"></i> Collection
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="form-label">Delivery Time</label>
                            <p style="font-size: 14px; font-weight: 700; color: #1a1a1a; margin: 0;">{{ $order->time }}</p>
                        </div>
                    </div>
                </div>

                <!-- Customer Info Card -->
                <div class="checkout-card">
                    <h5 class="checkout-title">Customer Information</h5>
                    
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px;">
                        <div>
                            <label class="form-label">Full Name</label>
                            <p style="font-size: 13px; font-weight: 600; color: #1a1a1a; margin: 0;">{{ $order->first_name }} {{ $order->last_name }}</p>
                        </div>
                        <div>
                            <label class="form-label">Email</label>
                            <p style="font-size: 13px; font-weight: 600; color: #1a1a1a; margin: 0;">{{ $order->email }}</p>
                        </div>
                        <div>
                            <label class="form-label">Phone</label>
                            <p style="font-size: 13px; font-weight: 600; color: #1a1a1a; margin: 0;">{{ $order->phone }}</p>
                        </div>
                        <div>
                            <label class="form-label">Address</label>
                            <p style="font-size: 13px; font-weight: 600; color: #1a1a1a; margin: 0; line-height: 1.5;">
                                {{ $order->address_1 }}
                                @if($order->address_2)
                                    <br>{{ $order->address_2 }}
                                @endif
                                <br>{{ $order->city }}, {{ $order->postcode }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Order Items Card -->
                <div class="checkout-card">
                    <h5 class="checkout-title">Order Items</h5>
                    
                    <div class="checkout-cart-body">
                        @foreach($order->items as $item)
                        <div class="cart-item-row">
                            <div style="display: grid; grid-template-columns: auto 1fr; gap: 12px; width: 100%;">
                                <div>
                                    <div class="cart-item-img" style="width: 60px; height: 60px; background: linear-gradient(135deg, #fff5eb, #ffe8d6);"></div>
                                </div>
                                <div>
                                    <p class="cart-product-name">{{ $item->product_name }}</p>
                                    @if($item->options->count() > 0)
                                        <ul class="cart-item-options">
                                            @foreach($item->options as $option)
                                                <li>{{ $option->option_name }}@if($option->price > 0) +£{{ number_format($option->price, 2) }}@endif</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <div class="cart-item-controls">
                                        <span class="cart-product-price">£{{ number_format($item->price, 2) }}</span>
                                        <span style="font-size: 12px; font-weight: 600; color: #666;">x{{ $item->quantity }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Special Instructions -->
                @if($order->notes)
                <div class="checkout-card">
                    <h5 class="checkout-title">Special Instructions</h5>
                    <div class="alert alert-info mb-0" style="background: linear-gradient(135deg, #d1ecf1, #bee5eb); border-left: 4px solid #17a2b8;">
                        {{ $order->notes }}
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column: Order Summary -->
            <div class="col-lg-4">
                <div class="order-summary-card">
                    <h5 class="checkout-title">Order Summary</h5>

                    <div class="checkout-cart-body" style="max-height: none; padding: 0; margin-bottom: 0;">
                        @foreach($order->items as $item)
                        <div class="cart-item-row" style="margin-bottom: 8px;">
                            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                                <div>
                                    <p class="cart-product-name" style="margin: 0;">{{ $item->product_name }}</p>
                                    <span style="font-size: 11px; color: #999;">x{{ $item->quantity }}</span>
                                </div>
                                <span class="cart-product-price">£{{ number_format($item->total, 2) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="summary-divider"></div>

                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>£{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Delivery Charge</span>
                        <span>£{{ number_format($order->delivery_charge, 2) }}</span>
                    </div>
                    @if($order->coupon_discount > 0)
                    <div class="summary-row" style="color: #28a745;">
                        <span>Promo Discount</span>
                        <span style="color: #28a745;">-£{{ number_format($order->coupon_discount, 2) }}</span>
                    </div>
                    @endif
                    @if($order->points_used > 0)
                    <div class="summary-row" style="color: #28a745;">
                        <span>Points Discount</span>
                        <span style="color: #28a745;">-£{{ number_format($order->points_used, 2) }}</span>
                    </div>
                    @endif

                    <div class="summary-divider"></div>

                    <div class="summary-row total">
                        <span>Total</span>
                        <span>£{{ number_format($order->total, 2) }}</span>
                    </div>

                    <!-- Payment Status -->
                    <div class="payment-badge" style="margin-top: 20px;">
                        <i class="fas fa-check-circle"></i>
                        <div class="payment-info">
                            <strong>
                                @if($order->payment_status === 'paid')
                                    Payment Confirmed
                                @elseif($order->payment_status === 'pending')
                                    Payment Pending
                                @else
                                    Payment Failed
                                @endif
                            </strong>
                            <small>
                                @if($order->payment_method === 'stripe')
                                    Stripe
                                @elseif($order->payment_method === 'paypal')
                                    PayPal
                                @else
                                    Cash on Delivery
                                @endif
                            </small>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div style="display: flex; flex-direction: column; gap: 10px; margin-top: 20px;">
                        <a href="/" class="btn-place-order" style="background: linear-gradient(135deg, #ff8a00, #ff5a00); margin: 0;">
                            <i class="fas fa-home"></i> Back to Home
                        </a>
                        <a href="/menu" class="btn-place-order" style="background: white; color: #ff8a00; border: 2px solid #ff8a00; margin: 0;">
                            <i class="fas fa-utensils"></i> Order Again
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection