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
            @foreach($birthdayVouchers as $coupon)
                <div class="referral-code" style="margin-bottom: 15px;">
                    <div class="referral-text">
                        <small>{{ $coupon->name ?? 'Birthday Voucher' }}</small>
                        <strong>{{ $coupon->code }}</strong>
                    </div>
                    <button class="btn-copy-referral birthday-copy-btn" data-code="{{ $coupon->code }}">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Regular Coupons -->
    <h5 style="margin-bottom: 15px;">Regular Offers</h5>
    @forelse($regularCoupons as $coupon)
        <div class="referral-code" style="margin-bottom: 15px;">
            <div class="referral-text">
                <small>{{ $coupon->name ?? 'Regular Offer' }}</small>
                <strong>{{ $coupon->code }}</strong>
                <div style="font-size: 12px; color: #666; margin-top: 4px;">
                    @if($coupon->discount_type === 'percent')
                        {{ number_format($coupon->discount_value, 0) }}% Off
                    @else
                        £{{ number_format($coupon->discount_value, 2) }} Off
                    @endif
                </div>
            </div>
            <button class="btn-copy-referral regular-copy-btn" data-code="{{ $coupon->code }}">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    @empty
        <p class="text-center">No active coupons available at the moment.</p>
    @endforelse
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('.birthday-copy-btn, .regular-copy-btn').click(function() {
            const code = $(this).data('code');
            navigator.clipboard.writeText(code);
            showSuccess('Coupon code ' + code + ' copied!');
        });
    });
</script>
@endsection