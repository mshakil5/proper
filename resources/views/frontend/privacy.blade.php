@extends('frontend.master')

@section('content')
<section class="find-us container p-3  align-items-center">
    <h2 class="big-title"><span style="color:var(--orange)">Privacy Policy</span></h2>
    <p class="subtitle">{!! $companyPrivacy->privacy_policy ?? '' !!}</p>
</section>
@endsection