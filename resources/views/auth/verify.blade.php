@extends('frontend.master')

@section('content')

<section class="auth-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-7 col-sm-10">
                <!-- Auth Card -->
                <div class="auth-card" data-aos="fade-up" data-aos-delay="100">
                    <!-- Header -->
                    <div class="auth-header">
                        <div class="auth-icon">
                            <i class="fas fa-envelope-open-text"></i>
                        </div>
                        <h2 class="auth-title">Verify Email</h2>
                        <p class="auth-subtitle">Confirm your email address</p>
                    </div>

                    <!-- Content -->
                    <div class="auth-form">
                        @if (session('resent'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Verification Link Sent</strong>
                                    <p>A fresh verification link has been sent to your email address.</p>
                                </div>
                            </div>
                        @endif

                        <div style="text-align: center; margin-bottom: 24px; line-height: 1.6; color: #666; font-size: 14px;">
                            <p style="margin-bottom: 12px;">
                                <i class="fas fa-info-circle" style="color: #ff8a00; margin-right: 8px;"></i>
                                Before proceeding, please check your email for a verification link.
                            </p>
                            <p style="margin: 0;">
                                If you did not receive the email, click the button below to request another verification link.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('verification.resend') }}" onsubmit="let b=this.querySelector('[type=submit]');b.disabled=true;b.innerHTML='<span>Please wait...</span><i class=\'fas fa-spinner fa-spin\'></i>';">
                            @csrf

                            <button type="submit" class="btn-auth-primary">
                                <span>Resend Verification Link</span>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </form>

                        <!-- Divider -->
                        <div class="auth-divider">Need help?</div>

                        <!-- Back to Login -->
                        <a href="{{ route('login') }}" class="btn-auth-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </div>

                <!-- Info Cards -->
                <div style="margin-top: 40px; padding: 20px; background: white; border-radius: 14px; border: 1px solid rgba(255, 138, 0, 0.1); box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);">
                    <h6 style="font-size: 12px; font-weight: 700; color: #1a1a1a; margin-bottom: 16px; text-transform: uppercase; letter-spacing: 0.3px;">
                        <i class="fas fa-lightbulb" style="color: #ff8a00; margin-right: 8px;"></i> Verification Tips
                    </h6>
                    
                    <ul style="list-style: none; padding: 0; margin: 0;">
                        <li style="padding: 10px 0; border-bottom: 1px solid #E8E8E8; font-size: 13px; color: #666;">
                            <strong style="color: #1a1a1a;">Check Spam Folder</strong> - The verification email might end up in your spam folder
                        </li>
                        <li style="padding: 10px 0; border-bottom: 1px solid #E8E8E8; font-size: 13px; color: #666;">
                            <strong style="color: #1a1a1a;">Link Expiration</strong> - Verification links expire after 24 hours
                        </li>
                        <li style="padding: 10px 0; font-size: 13px; color: #666;">
                            <strong style="color: #1a1a1a;">Check Email</strong> - Make sure you entered the correct email address
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection