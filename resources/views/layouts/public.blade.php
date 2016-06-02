@extends('layouts.base')

@section('page-includes')
    <script src="/js/modernizr.js?version={{ env('CACHE_BUST') }}"></script>
    <script src="/js/lib.js?version={{ env('CACHE_BUST') }}"></script>
    <script src="/js/app.js?version={{ env('CACHE_BUST') }}"></script>
    <link rel="stylesheet" href="/css/app.css?version={{ env('CACHE_BUST') }}" />
@endsection

@section('page-top')
    @include('elements.nav')
@endsection

@section('page-bottom')
    @include('elements.footer')
@endsection
