@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    <h3 class="dashboard-title">
        <i class="fas fa-gift"></i>
        My Gift Cards ({{ auth()->user()->purchasedGiftCards()->count() }})
    </h3>
    <p class="dashboard-subtitle">View and manage your purchased gift cards</p>

    <div class="gift-cards-grid">
        @forelse($giftCards as $giftCard)
            <div class="gift-card-display">
                <div class="gift-card-visual">
                    <div class="gift-card-coupon">
                        <div class="coupon-border"></div>
                        
                        <div class="coupon-content">
                            <div class="coupon-left">
                                <i class="fas fa-shopping-bag"></i>
                            </div>

                            <div class="coupon-middle">
                                <div class="coupon-branding">
                                    <div class="brand-logo"><img src={{ asset('uploads/company/' . $logo) }} style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;"></div>
                                    <small class="brand-url">www.propertakeaways.co.uk</small>
                                </div>
                                
                                <div class="coupon-amount">
                                    £{{ number_format($giftCard->balance, 2) }}
                                </div>
                                
                                <div class="coupon-label">
                                    OFF GIFT CARD
                                </div>
                            </div>

                            <div class="coupon-right">
                                <div class="coupon-qr">
                                    <i class="fas fa-qrcode"></i>
                                </div>
                            </div>
                        </div>

                        <div class="coupon-code">
                            {{ $giftCard->code }}
                        </div>

                        <div class="coupon-status-badge badge-{{ $giftCard->status }}">
                            {{ ucfirst($giftCard->status) }}
                        </div>
                    </div>
                </div>

                <div class="gift-card-actions">
                    <button type="button" class="btn-action btn-copy" onclick="copyGiftCard('{{ $giftCard->code }}')">
                        <i class="fas fa-copy"></i>
                        Copy Code
                    </button>
                    <button type="button" class="btn-action btn-download" onclick="downloadGiftCard('{{ $giftCard->code }}', '{{ $giftCard->balance }}')">
                        <i class="fas fa-download"></i>
                        Download
                    </button>
                    <button type="button" class="btn-action btn-share" onclick="shareGiftCard('{{ $giftCard->code }}')">
                        <i class="fas fa-share-alt"></i>
                        Share
                    </button>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-inbox"></i>
                <p>No gift cards purchased yet.</p>
                <a href="{{ route('gift-cards') }}" class="btn btn-other">Buy Gift Card Now</a>
            </div>
        @endforelse
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script>
function copyGiftCard(code) {
    navigator.clipboard.writeText(code).then(() => {
        showSuccess('Gift card code copied!');
    }).catch(() => {
        showError('Failed to copy gift card code');
    });
}

function downloadGiftCard(code, balance) {
    const element = event.target.closest('.gift-card-display').querySelector('.gift-card-coupon');
    
    html2canvas(element, {
        backgroundColor: '#f5e6d3',
        scale: 2,
        logging: false
    }).then(canvas => {
        const link = document.createElement('a');
        link.href = canvas.toDataURL('image/png');
        link.download = `giftcard-${code}.png`;
        link.click();
        showSuccess('Gift card downloaded!');
    }).catch(() => {
        showError('Failed to download gift card');
    });
}

function shareGiftCard(code) {
    const text = `Check out this gift card! Code: ${code}`;
    
    if (navigator.share) {
        navigator.share({
            title: 'Gift Card',
            text: text,
            url: window.location.href
        }).catch(() => {
            showError('Failed to share');
        });
    } else {
        navigator.clipboard.writeText(text).then(() => {
            showSuccess('Gift card info copied to clipboard!');
        }).catch(() => {
            showError('Failed to copy');
        });
    }
}
</script>

@endsection