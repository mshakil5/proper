@extends('frontend.master')

@section('content')
<section class="pricing section light-background">
  <div class="container">
    <div class="row gy-4 justify-content-center">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="pricing-item recommended">
          <h2 class="text-center mb-4">Reset Password</h2>

          <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
              <input id="email" type="email"
                     class="form-control @error('email') is-invalid @enderror"
                     name="email" value="{{ $email ?? old('email') }}"
                     required autocomplete="email" autofocus
                     placeholder="Email">
              @error('email')
                <div class="text-danger mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <input id="password" type="password"
                     class="form-control @error('password') is-invalid @enderror"
                     name="password" required autocomplete="new-password"
                     placeholder="New Password">
              @error('password')
                <div class="text-danger mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <input id="password-confirm" type="password"
                     class="form-control"
                     name="password_confirmation" required autocomplete="new-password"
                     placeholder="Confirm Password">
            </div>

            <div class="text-center mt-4">
              <button type="submit" class="btn-buy w-100">Reset Password</button>
            </div>

            <div class="text-center mt-3">
              <a href="{{ route('login') }}">Remember your password? Login</a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection