@extends('frontend.master')

@section('content')
<section class="pricing section light-background">
  <div class="container">
    <div class="row gy-4 justify-content-center">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="pricing-item recommended">
          <h2 class="text-center mb-4">Reset Password</h2>

          @if (session('status'))
            <div class="alert alert-success" role="alert">
              {{ session('status') }}
            </div>
          @endif

          <p class="text-center mb-4">Enter your email address to receive a password reset link.</p>

          <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-3">
              <input id="email" type="email"
                     class="form-control @error('email') is-invalid @enderror"
                     name="email" value="{{ old('email') }}"
                     required autocomplete="email" autofocus
                     placeholder="Email">
              @error('email')
                <div class="text-danger mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="text-center mt-4">
              <button type="submit" class="btn-buy w-100">Send Password Reset Link</button>
            </div>

            <div class="text-center mt-3">
              <a href="{{ route('login') }}">Remembered your password? Login</a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection