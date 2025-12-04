@extends('admin.master')

@section('content')
<section class="content">
  <div class="container-fluid">
    <div class="row pt-3">
      <div class="col-md-6 col-sm-6 col-12">
        <a href="{{ route('products.index') }}" class="text-dark">
          <div class="info-box shadow-lg">
            <span class="info-box-icon bg-primary"><i class="fas fa-box"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Services</span>
              <span class="info-box-number">{{ number_format($productsCount, 0) }}</span>
            </div>
          </div>
        </a>
      </div>

      <div class="col-md-6 col-sm-6 col-12">
        <a href="{{ route('contacts.index') }}" class="text-dark">
          <div class="info-box shadow-lg">
            <span class="info-box-icon bg-danger"><i class="far fa-envelope"></i></span>
            <div class="info-box-content">
              <span class="info-box-text">Unread Messages</span>
              <span class="info-box-number">{{ number_format($unreadMessagesCount, 0) }}</span>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>
</section>
@endsection

@section('script')

@endsection