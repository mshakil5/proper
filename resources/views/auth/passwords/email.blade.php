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
                            <i class="fas fa-key"></i>
                        </div>
                        <h2 class="auth-title">Reset Password</h2>
                        <p class="auth-subtitle">Recover access to your account</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('password.email') }}" class="auth-form" onsubmit="let b=this.querySelector('[type=submit]');b.disabled=true;b.innerHTML='<span>Please wait...</span><i class=\'fas fa-spinner fa-spin\'></i>';">
                        @csrf

                        <!-- Status Message -->
                        @if (session('status'))
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i>
                                <div>
                                    <strong>Email Sent</strong>
                                    <p>{{ session('status') }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Email Field -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-envelope"></i> Email Address <span class="required">*</span>
                            </label>
                            <input 
                                id="email" 
                                type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                name="email" 
                                value="{{ old('email') }}"
                                required 
                                autocomplete="email" 
                                autofocus
                                placeholder="Enter your email address">
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-auth-primary">
                            <span>Send Reset Link</span>
                            <i class="fas fa-paper-plane"></i>
                        </button>

                        <!-- Divider -->
                        <div class="auth-divider">Remember your password?</div>

                        <!-- Login Link -->
                        <a href="{{ route('login') }}" class="btn-auth-secondary">
                            <i class="fas fa-sign-in-alt"></i> Back to Login
                        </a>
                    </form>
                </div>

                <!-- Features -->
                <div class="auth-features" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <i class="fas fa-clock"></i>
                        <h6>Quick Reset</h6>
                        <p>Receive link in seconds</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-shield-alt"></i>
                        <h6>Secure</h6>
                        <p>Protected with encryption</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-check-circle"></i>
                        <h6>Easy</h6>
                        <p>Simple step-by-step process</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection