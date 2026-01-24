@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    <h3 class="dashboard-title">
        <i class="fas fa-truck"></i>
        Free Delivery Subscription
    </h3>
    <p class="dashboard-subtitle">£5.00 per month - Unlimited free delivery</p>

    <!-- Current Status -->
    @if($subscription && $subscription->isActive())
        <div class="status-card active">
            <div class="status-header">
                <i class="fas fa-check-circle"></i>
                <span class="status-badge">Active</span>
            </div>
            <div class="status-details">
                <p><strong>Valid Until:</strong> <span class="date-value">{{ $subscription->ends_at->format('M d, Y') }}</span></p>
            </div>
        </div>
    @else
        <div class="status-card inactive">
            <div class="status-header">
                <i class="fas fa-times-circle"></i>
                <span class="status-badge inactive">Inactive</span>
            </div>
            <p class="no-subscription-text">Get unlimited free delivery for just £5/month</p>
        </div>
    @endif

    <!-- Subscription Plan -->
    <div class="plan-container">
        <div class="plan-box">
            <div class="plan-icon">
                <i class="fas fa-gift"></i>
            </div>
            <h4 class="plan-title">Free Delivery Pass</h4>
            <div class="plan-price">
                <span class="amount">£5</span>
                <span class="period">/month</span>
            </div>

            <div class="plan-benefits">
                <p><i class="fas fa-check"></i> Unlimited free delivery</p>
                <p><i class="fas fa-check"></i> All orders included</p>
                <p><i class="fas fa-check"></i> No minimum spend</p>
                <p><i class="fas fa-check"></i> No contract</p>
                <p><i class="fas fa-check"></i> Pay monthly</p>
            </div>

            <button class="btn-subscribe" id="subscribeBtn">
                <i class="fas fa-credit-card"></i> 
                @if($subscription && $subscription->isActive())
                    Renew Now - {{ $subscription->ends_at->copy()->addMonthNoOverflow()->format('M d') }}
                @else
                    Subscribe Now
                @endif
            </button>
        </div>
    </div>

    <!-- Payment History -->
    @if($subscription)
        <div class="payment-history">
            <h5 class="history-title">Payment History</h5>
            <div class="history-table">
                @if($subscription->payments()->exists())
                    <table>
                        <thead>
                            <tr>
                                <th>Billing Month</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscription->payments()->orderByDesc('billing_month')->get() as $payment)
                                <tr>
                                    <td>{{ $payment->billing_month->format('M Y') }}</td>
                                    <td>£{{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ strtolower($payment->status) }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $payment->paid_at?->format('M d, Y') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="no-history">No payment history yet</p>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Loading Modal -->
<div id="loadingModal" style="position: fixed; inset: 0; background: rgba(0, 0, 0, 0.5); display: none; align-items: center; justify-content: center; z-index: 9999;">
    <div style="background: white; padding: 40px; border-radius: 16px; text-align: center; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
        <i class="fas fa-spinner fa-spin" style="font-size: 48px; color: #ff8a00; margin-bottom: 16px; display: block;"></i>
        <h4 style="margin: 0 0 8px; color: #1a1a1a;">Processing Payment</h4>
        <p style="margin: 0; color: #999; font-size: 14px;">Redirecting to payment...</p>
    </div>
</div>

@endsection

@section('script')
<script>
$(function() {

    @if(session('success'))
        showSuccess('{{ session("success") }}');
    @endif

    @if(session('error'))
        showError('{{ session("error") }}');
    @endif

    $('#subscribeBtn').on('click', function() {
        $('#loadingModal').css('display', 'flex');
        
        $.ajax({
            url: '{{ route("user.subscription.checkout") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                amount: 5.00
            },
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirectUrl;
                } else {
                    $('#loadingModal').css('display', 'none');
                    showError(response.message || 'Failed to process');
                }
            },
            error: function(xhr) {
                $('#loadingModal').css('display', 'none');
                showError(xhr.responseJSON?.message || 'Error processing subscription');
            }
        });
    });
});
</script>
@endsection