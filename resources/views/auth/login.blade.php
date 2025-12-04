@extends('frontend.master')

@section('content')

  <section id="login" class="contact section light-background d-flex align-items-center" style="min-height: 80vh;">
    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="d-flex justify-content-center">
        <div class="contact-form-container col-lg-6 col-md-8" data-aos="fade-up" data-aos-delay="300">
          <h3 class="text-center mb-3">Login</h3>
          <p class="text-center mb-4">Sign in to start your session</p>

          <form method="POST" action="{{ route('login') }}" class="php-email-form">
            @csrf
            <div class="form-group mb-3">
              <input id="email" type="email"
                    class="form-control @error('email') is-invalid @enderror"
                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
                    placeholder="Email">
              @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="form-group mb-3">
              <input id="password" type="password"
                    class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="current-password"
                    placeholder="Password">
              @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
              @enderror
            </div>

            <div class="text-center mt-4">
              <button type="submit" class="btn btn-primary w-50 mx-auto d-block" id="submit-btn">Sign In</button>
            </div>

            @if(session('error'))
              <div class="alert alert-danger mt-3 text-center">{{ session('error') }}</div>
            @endif
          </form>
        </div>
      </div>
    </div>
  </section>
@endsection