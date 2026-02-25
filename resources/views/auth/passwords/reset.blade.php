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
                            <i class="fas fa-lock"></i>
                        </div>
                        <h2 class="auth-title">Set New Password</h2>
                        <p class="auth-subtitle">Create a new secure password</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('password.update') }}" class="auth-form" onsubmit="let b=this.querySelector('[type=submit]');b.disabled=true;b.innerHTML='<span>Please wait...</span><i class=\'fas fa-spinner fa-spin\'></i>';">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

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
                                value="{{ $email ?? old('email') }}"
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

                        <!-- New Password Field -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock"></i> New Password <span class="required">*</span>
                            </label>
                            <div class="password-wrapper">
                                <input 
                                    id="password" 
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password" 
                                    required 
                                    autocomplete="new-password"
                                    placeholder="Enter new password">
                                <button type="button" class="password-toggle" onclick="togglePassword(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Confirm Password Field -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock"></i> Confirm Password <span class="required">*</span>
                            </label>
                            <div class="password-wrapper">
                                <input 
                                    id="password-confirm" 
                                    type="password"
                                    class="form-control"
                                    name="password_confirmation" 
                                    required 
                                    autocomplete="new-password"
                                    placeholder="Confirm your password">
                                <button type="button" class="password-toggle" onclick="togglePassword(this)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-auth-primary">
                            <span>Reset Password</span>
                            <i class="fas fa-check"></i>
                        </button>

                        <!-- Divider -->
                        <div class="auth-divider">Remember your password?</div>

                        <!-- Login Link -->
                        <a href="{{ route('login') }}" class="btn-auth-secondary">
                            <i class="fas fa-sign-in-alt"></i> Back to Login
                        </a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('script')
<script>
function togglePassword(btn) {
    const input = btn.previousElementSibling;
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<i class="fas fa-eye-slash"></i>';
    } else {
        input.type = 'password';
        btn.innerHTML = '<i class="fas fa-eye"></i>';
    }
}
</script>
@endsection