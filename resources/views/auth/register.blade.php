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
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h2 class="auth-title">Create Account</h2>
                        <p class="auth-subtitle">Register to get started</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('register') }}" class="auth-form">
                        @csrf

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i> First Name <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control @error('first_name') is-invalid @enderror"
                                name="first_name"
                                value="{{ old('first_name') }}"
                                required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i> Last Name <span class="required">*</span>
                            </label>
                            <input
                                type="text"
                                class="form-control @error('last_name') is-invalid @enderror"
                                name="last_name"
                                value="{{ old('last_name') }}"
                                required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

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
                                placeholder="Enter your email">
                            @error('email')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Phone Field -->
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-phone"></i> Phone Number <span class="required">*</span>
                            </label>
                            <input 
                                id="phone" 
                                type="tel"
                                class="form-control @error('phone') is-invalid @enderror"
                                name="phone" 
                                value="{{ old('phone') }}" 
                                required 
                                autocomplete="tel"
                                placeholder="Enter your phone number">
                            @error('phone')
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
                                    autocomplete="new-password"
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

                        <!-- Terms Agreement -->
                        <div style="margin-bottom: 20px;">
                            <label class="remember-checkbox">
                                <input type="checkbox" name="terms" id="regTerms" required>
                                <span>I agree to the <a href="#" style="color: #ff8a00; text-decoration: none;">Terms & Conditions</a></span>
                            </label>
                        </div>

                        <!-- Error Alert -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <div>
                                    <strong>Registration Failed</strong>
                                    <p>Please check the errors above</p>
                                </div>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <button type="submit" class="btn-auth-primary">
                            <span>Create Account</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>

                        <!-- Divider -->
                        <div class="auth-divider">Already have an account?</div>

                        <!-- Login Link -->
                        <a href="{{ route('login') }}" class="btn-auth-secondary">
                            <i class="fas fa-sign-in-alt"></i> Sign In
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