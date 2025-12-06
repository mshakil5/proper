@extends('frontend.master')

@section('content')
<section class="find-us container p-3  align-items-center">
    <h2 class="big-title"><span style="color:var(--orange)">Terms & Conditions</span></h2>
    <p class="subtitle">{!! $terms->terms_and_conditions ?? '' !!}</p>
</section>
@endsection