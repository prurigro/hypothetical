@extends('templates.base', [ 'title' => 'Dashboard' ])

@section('page-includes')
    <script src="/js/lib-dashboard.js?version={{ env('CACHE_BUST') }}"></script>
    <script src="/js/dashboard.js?version={{ env('CACHE_BUST') }}"></script>
    <link rel="stylesheet" href="/css/dashboard.css?version={{ env('CACHE_BUST') }}" />
@endsection

@section('page-top')
    @include('dashboard.nav')
@endsection
