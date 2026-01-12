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
                            <i class="fas fa-sign-in-alt"></i>
                        </div>
                        <h2 class="auth-title">Welcome Back</h2>
                        <p class="auth-subtitle">Sign in to your account</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('login') }}" class="auth-form">
                        @csrf

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
                                placeholder="Enter your email">
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-lock"></i> Password <span class="required">*</span>
                            </label>
                            <div class="password-wrapper">
                                <input 
                                    id="password" 
                                    type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    name="password" 
                                    required 
                                    autocomplete="current-password"
                                    placeholder="Enter your password">
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

                        <!-- Remember & Forgot -->
                        <div class="auth-options">
                            <label class="remember-checkbox">
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span>Remember me</span>
                            </label>
                            <a href="{{ route('password.request') }}" class="forgot-link">Forgot password?</a>
                        </div>

                        <!-- Error Alert -->
                        @if(session('error'))
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <div>
                                    <strong>Login Failed</strong>
                                    <p>{{ session('error') }}</p>
                                </div>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <button type="submit" class="btn-auth-primary">
                            <span>Sign In</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>

                        <!-- Divider -->
                        <div class="auth-divider">Don't have an account?</div>

                        <!-- Register Link -->
                        <a href="{{ route('register') }}" class="btn-auth-secondary">
                            <i class="fas fa-user-plus"></i> Create Account
                        </a>
                    </form>
                </div>

                <!-- Features -->
                <div class="auth-features" data-aos="fade-up" data-aos-delay="200">
                    <div class="feature-card">
                        <i class="fas fa-shopping-cart"></i>
                        <h6>Quick Orders</h6>
                        <p>Faster checkout with saved info</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-history"></i>
                        <h6>Order History</h6>
                        <p>View your past orders</p>
                    </div>
                    <div class="feature-card">
                        <i class="fas fa-gift"></i>
                        <h6>Exclusive Offers</h6>
                        <p>Special discounts & rewards</p>
                    </div>
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