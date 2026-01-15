<!-- resources/views/user/social-sharing.blade.php -->

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
                <strong>REF2025JOHN</strong>
            </div>
            <button class="btn-copy-referral" onclick="copyReferral('REF2025JOHN')">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    </div>

    <div class="user-dashboard-card" style="margin-top: 32px;">
        <div class="social-sharing-section">
            <h4 class="sharing-title">Share on Social Media</h4>
            <p class="sharing-subtitle">Share your referral link and earn extra points</p>
            
            <div class="sharing-buttons">
                <button class="social-btn social-btn-facebook" title="Share on Facebook">
                    <i class="fab fa-facebook-f"></i>
                </button>
                <button class="social-btn social-btn-twitter" title="Share on Twitter">
                    <i class="fab fa-twitter"></i>
                </button>
                <button class="social-btn social-btn-whatsapp" title="Share on WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                </button>
                <button class="social-btn social-btn-linkedin" title="Share on LinkedIn">
                    <i class="fab fa-linkedin-in"></i>
                </button>
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
                <div class="stat-box-icon"><i class="fas fa-users"></i></div>
                <div class="stat-box-value">8</div>
                <div class="stat-box-label">Friends Invited</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-box-value">6</div>
                <div class="stat-box-label">Successfully Joined</div>
            </div>
            <div class="stat-box">
                <div class="stat-box-icon"><i class="fas fa-gift"></i></div>
                <div class="stat-box-value">300</div>
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
            <div class="user-nav-item" style="background: white; border-left: 4px solid #28a745; padding: 14px 16px; display: flex; justify-content: space-between;">
                <div>
                    <p style="margin: 0 0 4px; font-weight: 700; color: #1a1a1a; font-size: 13px;">Sarah Johnson</p>
                    <p style="margin: 0; color: #999; font-size: 12px;">Joined 5 days ago</p>
                </div>
                <p style="margin: 0; color: #28a745; font-weight: 800; font-size: 14px;">+50 Points</p>
            </div>

            <div class="user-nav-item" style="background: white; border-left: 4px solid #28a745; padding: 14px 16px; display: flex; justify-content: space-between;">
                <div>
                    <p style="margin: 0 0 4px; font-weight: 700; color: #1a1a1a; font-size: 13px;">Mike Chen</p>
                    <p style="margin: 0; color: #999; font-size: 12px;">Joined 12 days ago</p>
                </div>
                <p style="margin: 0; color: #28a745; font-weight: 800; font-size: 14px;">+50 Points</p>
            </div>

            <div class="user-nav-item" style="background: white; border-left: 4px solid #28a745; padding: 14px 16px; display: flex; justify-content: space-between;">
                <div>
                    <p style="margin: 0 0 4px; font-weight: 700; color: #1a1a1a; font-size: 13px;">Emma Wilson</p>
                    <p style="margin: 0; color: #999; font-size: 12px;">Joined 20 days ago</p>
                </div>
                <p style="margin: 0; color: #28a745; font-weight: 800; font-size: 14px;">+50 Points</p>
            </div>
        </div>
    </div>
</div>

<script>
function copyReferral(code) {
    navigator.clipboard.writeText(code).then(() => {
        alert('Referral code copied to clipboard!');
    });
}
</script>

@endsection