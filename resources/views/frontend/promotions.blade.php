@extends('frontend.master')

@section('content')
<section class="find-us container p-3  align-items-center">
    <h1 class="big-title"><span style="color:var(--orange)">Earn Points</span></h1>
    <p class="subtitle">{!! $promotions->promotions_description ?? '' !!}</p>
</section>
@endsection