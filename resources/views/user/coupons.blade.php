@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    <h3 class="dashboard-title">
        <i class="fas fa-ticket-alt"></i>
        Available Coupons and Vouchers ({{ \App\Models\Coupon::active()->count() }})
    </h3>
    <p class="dashboard-subtitle">Claim and use exciting discounts on your next order</p>

    <div class="coupons-grid">
        @forelse(\App\Models\Coupon::active()->get() as $coupon)
            <div class="coupon-card">
                <div class="coupon-content">
                    <div class="coupon-code">{{ $coupon->code }}</div>
                    <p class="coupon-description">{{ $coupon->name ?? '' }}</p>
                </div>
                <div>
                    <div class="coupon-discount">
                        @if($coupon->discount_type === 'percent')
                            {{ number_format($coupon->discount_value, 0) }}%
                        @else
                            £{{ number_format($coupon->discount_value, 2) }}
                        @endif
                    </div>
                    <button type="button" class="btn-copy-coupon" onclick="copyCoupon('{{ $coupon->code }}')">
                        Copy Code
                    </button>
                </div>
            </div>
        @empty
            <p class="text-center">No active coupons and vouchers available at the moment.</p>
        @endforelse
    </div>
</div>

<script>
function copyCoupon(code) {
    navigator.clipboard.writeText(code).then(() => {
        showSuccess('Coupon code ' + code + ' copied!');
    }).catch(() => {
        showError('Failed to copy coupon code');
    });
}
</script>

@endsection