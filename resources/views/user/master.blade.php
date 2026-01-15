@extends('frontend.master')

@section('content')
    <div class="user-portal-container">
        <div class="row">
            <div class="col-md-3">
                @include('user.sidebar')
            </div>
            <div class="col-md-9">
                @yield('user-content')
            </div>
        </div>
    </div>
@endsection