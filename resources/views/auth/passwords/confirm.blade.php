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
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h2 class="auth-title">Confirm Password</h2>
                        <p class="auth-subtitle">Verify your identity to continue</p>
                    </div>

                    <!-- Form -->
                    <form method="POST" action="{{ route('password.confirm') }}" class="auth-form" onsubmit="let b=this.querySelector('[type=submit]');b.disabled=true;b.innerHTML='<span>Please wait...</span><i class=\'fas fa-spinner fa-spin\'></i>';">
                        @csrf

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

                        <!-- Error Alert -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                                <div>
                                    <strong>Password Incorrect</strong>
                                    <p>The password you entered is incorrect. Please try again.</p>
                                </div>
                            </div>
                        @endif

                        <!-- Submit Button -->
                        <button type="submit" class="btn-auth-primary">
                            <span>Confirm Password</span>
                            <i class="fas fa-check"></i>
                        </button>

                        <!-- Divider -->
                        <div class="auth-divider">Need assistance?</div>

                        <!-- Forgot Password Link -->
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="btn-auth-secondary">
                                <i class="fas fa-key"></i> Forgot Your Password?
                            </a>
                        @endif
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