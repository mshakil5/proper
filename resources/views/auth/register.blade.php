@extends('frontend.master')

@section('content')
<section class="pricing section light-background">
  <div class="container">
    <div class="row gy-4 justify-content-center">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="pricing-item recommended">
          <h2 class="text-center mb-4">Register</h2>

          <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-3">
              <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                     name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                     placeholder="Enter your full name">
              @error('name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                     name="email" value="{{ old('email') }}" required autocomplete="email"
                     placeholder="Enter your email">
              @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <input id="password" type="password"
                     class="form-control @error('password') is-invalid @enderror"
                     name="password" required autocomplete="new-password"
                     placeholder="Enter password">
              @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <input id="password-confirm" type="password" class="form-control"
                     name="password_confirmation" required autocomplete="new-password"
                     placeholder="Confirm password">
            </div>

            <div class="text-center mt-4">
              <button type="submit" class="btn-buy w-100">Register</button>
            </div>

            <div class="text-center mt-3">
              <a href="{{ route('login') }}">Already have an account? Login</a>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>
@endsection