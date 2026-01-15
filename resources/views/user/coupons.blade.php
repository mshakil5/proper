<!-- resources/views/user/coupons.blade.php -->

@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    <h3 class="dashboard-title">
        <i class="fas fa-ticket-alt"></i>
        Available Coupons
    </h3>
    <p class="dashboard-subtitle">Claim and use exciting discounts on your next order</p>

    <div class="coupons-grid">
        <div class="coupon-card">
            <div class="coupon-content">
                <div class="coupon-code">PIZZA20</div>
                <p class="coupon-description">20% Off on Pizza Orders</p>
            </div>
            <div>
                <div class="coupon-discount">20%</div>
                <button class="btn-copy-coupon" onclick="copyCoupon('PIZZA20')">Copy Code</button>
            </div>
        </div>

        <div class="coupon-card">
            <div class="coupon-content">
                <div class="coupon-code">SAVE10</div>
                <p class="coupon-description">$10 Off on Orders Above $50</p>
            </div>
            <div>
                <div class="coupon-discount">$10</div>
                <button class="btn-copy-coupon" onclick="copyCoupon('SAVE10')">Copy Code</button>
            </div>
        </div>

        <div class="coupon-card">
            <div class="coupon-content">
                <div class="coupon-code">FREEDELIV</div>
                <p class="coupon-description">Free Delivery on Your Next Order</p>
            </div>
            <div>
                <div class="coupon-discount">FREE</div>
                <button class="btn-copy-coupon" onclick="copyCoupon('FREEDELIV')">Copy Code</button>
            </div>
        </div>

        <div class="coupon-card">
            <div class="coupon-content">
                <div class="coupon-code">SUMMER15</div>
                <p class="coupon-description">15% Off on All Summer Items</p>
            </div>
            <div>
                <div class="coupon-discount">15%</div>
                <button class="btn-copy-coupon" onclick="copyCoupon('SUMMER15')">Copy Code</button>
            </div>
        </div>

        <div class="coupon-card">
            <div class="coupon-content">
                <div class="coupon-code">LOYALTY25</div>
                <p class="coupon-description">25% Loyalty Discount</p>
            </div>
            <div>
                <div class="coupon-discount">25%</div>
                <button class="btn-copy-coupon" onclick="copyCoupon('LOYALTY25')">Copy Code</button>
            </div>
        </div>

        <div class="coupon-card">
            <div class="coupon-content">
                <div class="coupon-code">WEEKEND5</div>
                <p class="coupon-description">Extra 5% Off on Weekends</p>
            </div>
            <div>
                <div class="coupon-discount">5%</div>
                <button class="btn-copy-coupon" onclick="copyCoupon('WEEKEND5')">Copy Code</button>
            </div>
        </div>
    </div>
</div>

<script>
function copyCoupon(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('Coupon code ' + code + ' copied to clipboard!');
    });
}
</script>

@endsection