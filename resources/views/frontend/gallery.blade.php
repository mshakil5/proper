@extends('frontend.master')

@section('content')

    @include('frontend.partials.banner', [
        'title' => 'Gallery',
        'image' => $page->banner_image ?? asset('banner.jpg'),
    ])

    @include('frontend.partials.gallery')

@endsection