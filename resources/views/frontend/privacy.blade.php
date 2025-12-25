@extends('frontend.master')

@section('content')
<section class="find-us container p-3  align-items-center">
    <h1 class="big-title"><span style="color:var(--orange)">Privacy Policy</span></h1>
    <p class="subtitle">{!! $companyPrivacy->privacy_policy ?? '' !!}</p>
</section>
@endsection