@extends('frontend.master')

@section('content')
<section class="find-us container p-3  align-items-center">
    <h1 class="big-title"><span style="color:var(--orange)">Terms & Conditions</span></h1>
    <p class="subtitle">{!! $terms->terms_and_conditions ?? '' !!}</p>
</section>
@endsection