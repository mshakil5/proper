@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    <h3 class="dashboard-title">
        <i class="fas fa-ticket-alt"></i>
        Available Coupons and Vouchers
    </h3>
    <p class="dashboard-subtitle">Claim and use exciting discounts on your next order</p>

    <!-- Birthday Vouchers -->
    @if($birthdayVouchers->count() > 0)
        <div style="margin-bottom: 40px; padding: 20px; background: #fff9e6; border-left: 4px solid #ff8a00; border-radius: 8px;">
            <h5 style="color: #ff8a00; margin-bottom: 15px;">
                <i class="fas fa-birthday-cake"></i> Your Birthday Vouchers
            </h5>
            <div class="coupons-grid">
                @foreach($birthdayVouchers as $coupon)
                    <div class="coupon-card" style="border: 2px solid #ff8a00;">
                        <div class="coupon-content">
                            <span class="badge bg-danger" style="display: block; margin-bottom: 8px;">Birthday Gift</span>
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
                @endforeach
            </div>
        </div>
    @endif

    <!-- Regular Coupons -->
    <h5 style="margin-bottom: 15px;">Regular Offers</h5>
    <div class="coupons-grid">
        @forelse($regularCoupons as $coupon)
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
            <p class="text-center">No active coupons available at the moment.</p>
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