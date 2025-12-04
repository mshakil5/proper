@extends('frontend.master')

@section('content')
<section class="pricing section light-background">
  <div class="container">
    <div class="row gy-4 justify-content-center">
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="pricing-item recommended">
          <h2 class="text-center mb-4">Verify Your Email Address</h2>

          @if (session('resent'))
            <div class="alert alert-success" role="alert">
              A fresh verification link has been sent to your email address.
            </div>
          @endif

          <p class="text-center mb-3">Before proceeding, please check your email for a verification link.</p>
          <p class="text-center mb-4">If you did not receive the email,</p>

          <form method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <div class="text-center">
              <button type="submit" class="btn-buy w-100">Click here to request another</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection