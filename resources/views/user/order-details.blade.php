<!-- resources/views/user/order-details.blade.php -->

@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    <div class="order-details-header">
        <div class="order-details-info">
            <h4>Order #ORD123456</h4>
            <p><strong>Order Date:</strong> January 15, 2026</p>
            <p><strong>Delivery Address:</strong> 123 Main Street, New York, NY 10001</p>
        </div>
        <div class="order-details-status">
            <div class="status-badge">Delivered</div>
            <p>Delivered on Jan 17, 2026</p>
        </div>
    </div>

    <div class="order-timeline">
        <h5>Order Timeline</h5>
        
        <div class="timeline-item">
            <div class="timeline-dot">
                <i class="fas fa-check"></i>
            </div>
            <div class="timeline-content">
                <h6>Order Delivered</h6>
                <p>January 17, 2026 at 2:30 PM</p>
            </div>
        </div>

        <div class="timeline-item">
            <div class="timeline-dot">
                <i class="fas fa-truck"></i>
            </div>
            <div class="timeline-content">
                <h6>Out for Delivery</h6>
                <p>January 17, 2026 at 9:00 AM</p>
            </div>
        </div>

        <div class="timeline-item">
            <div class="timeline-dot">
                <i class="fas fa-box"></i>
            </div>
            <div class="timeline-content">
                <h6>Package Dispatched</h6>
                <p>January 16, 2026 at 3:45 PM</p>
            </div>
        </div>

        <div class="timeline-item">
            <div class="timeline-dot">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <div class="timeline-content">
                <h6>Order Confirmed</h6>
                <p>January 15, 2026 at 10:20 AM</p>
            </div>
        </div>
    </div>

    <div class="order-items-list">
        <h5>Order Items</h5>
        
        <div class="order-item">
            <img src="https://via.placeholder.com/70" alt="Product" class="order-item-img">
            <div class="order-item-content">
                <h6 class="order-item-name">Classic Margherita Pizza</h6>
                <p class="order-item-detail">Size: Large</p>
                <p class="order-item-detail">Quantity: 1</p>
                <p class="order-item-price">$15.50</p>
            </div>
        </div>

        <div class="order-item">
            <img src="https://via.placeholder.com/70" alt="Product" class="order-item-img">
            <div class="order-item-content">
                <h6 class="order-item-name">Garlic Bread</h6>
                <p class="order-item-detail">Size: Regular</p>
                <p class="order-item-detail">Quantity: 2</p>
                <p class="order-item-price">$12.00</p>
            </div>
        </div>

        <div class="order-item">
            <img src="https://via.placeholder.com/70" alt="Product" class="order-item-img">
            <div class="order-item-content">
                <h6 class="order-item-name">Coca Cola</h6>
                <p class="order-item-detail">Size: 500ml</p>
                <p class="order-item-detail">Quantity: 2</p>
                <p class="order-item-price">$6.00</p>
            </div>
        </div>
    </div>

    <div class="order-summary">
        <h5>Order Summary</h5>
        
        <div class="summary-row">
            <span>Subtotal</span>
            <span>$33.50</span>
        </div>
        <div class="summary-row">
            <span>Delivery Fee</span>
            <span>$5.00</span>
        </div>
        <div class="summary-row">
            <span>Discount (10% OFF)</span>
            <span>-$3.85</span>
        </div>
        <div class="summary-row">
            <span>Tax</span>
            <span>$2.85</span>
        </div>
        <div class="summary-row total">
            <span>Total Amount</span>
            <span>$37.50</span>
        </div>

        <div class="order-details-badge">
            <p><strong>Payment Method:</strong> Credit Card</p>
            <p><strong>Status:</strong> Paid</p>
        </div>

        <button class="btn-reorder-now">Order Again</button>
    </div>
</div>

@endsection