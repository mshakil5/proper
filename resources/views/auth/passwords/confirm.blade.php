@extends('frontend.master')

@section('content')
<section class="pricing section light-background">
  <div class="container">
    <div class="row gy-4 justify-content-center">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="pricing-item recommended">
          <h2 class="text-center mb-4">Confirm Password</h2>

          <p class="text-center">Please confirm your password before continuing.</p>

          <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="mb-3 position-relative">
              <label for="password" class="form-label">Password</label>
              <input id="password" type="password"
                     class="form-control @error('password') is-invalid @enderror"
                     name="password" required autocomplete="current-password"
                     placeholder="Enter your password">
              @error('password')
                <div class="invalid-feedback d-block">{{ $message }}</div>
              @enderror
            </div>

            <div class="text-center mt-4">
              <button type="submit" class="btn-buy w-100">Confirm Password</button>
            </div>

            @if (Route::has('password.request'))
              <div class="text-center mt-3">
                <a href="{{ route('password.request') }}">Forgot Your Password?</a>
              </div>
            @endif

          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection