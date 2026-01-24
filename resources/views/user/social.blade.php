@extends('user.master')

@section('user-content')

<div class="user-dashboard-card">
    <h3 class="dashboard-title">
        <i class="fas fa-share-alt"></i>
        Social Sharing & Referral
    </h3>
    <p class="dashboard-subtitle">Share with friends and earn rewards</p>

    <div class="social-sharing-section">
        <h4 class="sharing-title">Share Your Referral Code</h4>
        <p class="sharing-subtitle">Invite friends and both of you get 50 bonus points!</p>
        
        <div class="referral-code">
            <div class="referral-text">
                <small>Your Referral Code</small>
                <strong>{{ auth()->user()->referral_code }}</strong>
            </div>
            <button class="btn-copy-referral" id="copyBtn">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    </div>

    @if(!auth()->user()->referred_by)
    <div class="user-dashboard-card" style="margin-top: 32px;">
        <div class="social-sharing-section">
            <h4 class="sharing-title">Use a Referral Code</h4>
            <p class="sharing-subtitle">Enter someone's referral code to earn bonus points</p>

            <form id="referralForm">
                @csrf
                <div class="row g-2 align-items-center">
                    <div class="col-md-10">
                        <input type="text" id="referralCodeInput" class="form-control" placeholder="Enter referral code" maxlength="20">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn-other" style="height: 40px">
                            <i class="fas fa-check"></i> Apply
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @else
    <div class="user-dashboard-card" style="margin-top: 32px;">
        <div class="social-sharing-section">
            <h4 class="sharing-title">Referral Code</h4>
            <p class="sharing-subtitle">You were referred by: <strong>{{ auth()->user()->referrer ? auth()->user()->referrer->first_name . ' ' . auth()->user()->referrer->last_name : '' }}</strong></p>
        </div>
    </div>
    @endif

    <div class="user-dashboard-card" style="margin-top: 32px;">
        <div class="social-sharing-section">
            <h4 class="sharing-title">Share on Social Media</h4>
            <p class="sharing-subtitle">Share and earn 10 points per platform (1 share/day, max 5 total)</p>
            
            <div class="sharing-buttons">
                <button class="social-btn social-btn-facebook" id="facebookBtn" title="Share on Facebook" {{ auth()->user()->canShareToday() ? '' : 'disabled' }}>
                    <i class="fab fa-facebook-f"></i>
                </button>
                <button class="social-btn social-btn-tiktok" id="tiktokBtn" title="Share on TikTok" {{ auth()->user()->canShareTikTokToday() ? '' : 'disabled' }}>
                    <i class="fab fa-tiktok"></i>
                </button>
                <button class="social-btn social-btn-instagram" id="instagramBtn" title="Share on Instagram" {{ auth()->user()->canShareInstagramToday() ? '' : 'disabled' }}>
                    <i class="fab fa-instagram"></i>
                </button>
            </div>
        </div>

        <div class="stats-grid" style="margin-top: 20px;">
            <div class="stat-box">
                <div class="stat-box-label">Facebook</div>
                <p style="margin: 8px 0 0; font-size: 12px; color: #999;">{{ auth()->user()->facebookShareStatusText() }}</p>
            </div>
            <div class="stat-box">
                <div class="stat-box-label">TikTok</div>
                <p style="margin: 8px 0 0; font-size: 12px; color: #999;">{{ auth()->user()->tikTokShareStatusText() }}</p>
            </div>
            <div class="stat-box">
                <div class="stat-box-label">Instagram</div>
                <p style="margin: 8px 0 0; font-size: 12px; color: #999;">{{ auth()->user()->instagramShareStatusText() }}</p>
            </div>
        </div>
    </div>

    <div class="user-dashboard-card" style="margin-top: 32px;">
        <h5 class="dashboard-title" style="font-size: 16px; margin-bottom: 20px;">
            <i class="fas fa-chart-bar"></i>
            Referral Statistics
        </h5>
        
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-box-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-box-value">{{ auth()->user()->getReferralCount() }}</div>
                <div class="stat-box-label">Successfully Joined</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-icon"><i class="fas fa-gift"></i></div>
                <div class="stat-box-value">{{ auth()->user()->getReferralPoints() }}</div>
                <div class="stat-box-label">Points Earned</div>
            </div>
        </div>
    </div>

    <div class="user-dashboard-card" style="margin-top: 32px;">
        <h5 class="dashboard-title" style="font-size: 16px; margin-bottom: 20px;">
            <i class="fas fa-history"></i>
            Recent Referrals
        </h5>
        
        <div style="display: grid; gap: 12px;">
            @forelse(auth()->user()->referralHistory() as $referral)
                <div class="user-nav-item" style="background: white; border-left: 4px solid #28a745; padding: 14px 16px; display: flex; justify-content: space-between;">
                    <div>
                        <p style="margin: 0 0 4px; font-weight: 700; color: #1a1a1a; font-size: 13px;">{{ $referral->name }}</p>
                        <p style="margin: 0; color: #999; font-size: 12px;">Joined {{ $referral->created_at->diffForHumans() }}</p>
                    </div>
                    <p style="margin: 0; color: #28a745; font-weight: 800; font-size: 14px;">+50 Points</p>
                </div>
            @empty
                <div style="text-align: center; padding: 20px; color: #999;">
                    <p>No referrals yet. Start sharing your code!</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
$(document).ready(function() {
    const referralCode = "{{ auth()->user()->referral_code }}";
    const referralUrl = "{{ route('register') }}?ref=" + referralCode;
    const shareRoute = "{{ route('user.social.share') }}";
    const applyReferralRoute = "{{ route('user.apply.referral') }}";

    // Copy referral code
    $('#copyBtn').click(function() {
        navigator.clipboard.writeText(referralCode);
        showSuccess('Referral code copied!');
    });

    // Submit referral code
    $('#referralForm').submit(function(e) {
        e.preventDefault();
        let code = $('#referralCodeInput').val().trim();

        if (!code) {
            showError('Please enter a referral code');
            return;
        }

        $.ajax({
            url: applyReferralRoute,
            type: 'POST',
            data: JSON.stringify({ referral_code: code }),
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(data) {
                console.log(data);
                if (data.success) {
                    showSuccess(data.message);
                    $('#referralCodeInput').val('');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    showError(data.message);
                }
            },
            error: function() {
                showError('Something went wrong. Try again!');
            }
        });
    });

    // Facebook sharing logic
    $('#facebookBtn').click(function() {
        const $btn = $(this);

        if ($btn.data('shared')) return;

        const shareText = "Check this out! Join using my referral code: " + referralCode;

        if (navigator.share) {
            navigator.share({
                title: 'Join Us',
                text: shareText,
                url: referralUrl
            }).then(() => {
                recordShare('facebook');
            }).catch(err => console.log('Share failed:', err));
        } else {
            window.open(
                `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(referralUrl)}&quote=${encodeURIComponent(shareText)}`,
                '_blank',
                'width=600,height=400'
            );
            recordShare('facebook');
        }
    });

    // TikTok sharing
    $('#tiktokBtn').click(function() {
        const $btn = $(this);
        if ($btn.data('shared')) return;

        const shareText = "Check this out! Join using my referral code: " + referralCode;
        
        window.open(
            `https://www.tiktok.com/share?url=${encodeURIComponent(referralUrl)}`,
            '_blank',
            'width=600,height=400'
        );
        recordShare('tiktok');
    });

    // Instagram sharing
    $('#instagramBtn').click(function() {
        const $btn = $(this);
        if ($btn.data('shared')) return;

        const shareText = "Check this out! Join using my referral code: " + referralCode;
        
        window.open(
            `https://www.instagram.com/?url=${encodeURIComponent(referralUrl)}`,
            '_blank',
            'width=600,height=400'
        );
        recordShare('instagram');
    });

    function recordShare(platform) {
        $.ajax({
            url: shareRoute,
            type: 'POST',
            data: JSON.stringify({ platform: platform }),
            contentType: 'application/json',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(data) {
                if (data.success) {
                    showSuccess(data.message);
                    if (platform === 'facebook') {
                        $('#facebookBtn').data('shared', true).prop('disabled', true);
                    } else if (platform === 'tiktok') {
                        $('#tiktokBtn').data('shared', true).prop('disabled', true);
                    } else if (platform === 'instagram') {
                        $('#instagramBtn').data('shared', true).prop('disabled', true);
                    }
                    location.reload();
                } else {
                    showError(data.message);
                }
            },
            error: function() {
                showError('Something went wrong while recording share.');
            }
        });
    }
});
</script>
@endsection