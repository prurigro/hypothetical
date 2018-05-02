@extends('templates.base', [ 'title' => 'Dashboard' ])
@set('current_page', preg_replace([ '/^.*\//', '/\/.*/' ], [ '', '' ], Request::url()))

@section('page-includes')
    <script src="/js/lib-dashboard.js?version={{ env('CACHE_BUST') }}"></script>
    <script src="/js/dashboard.js?version={{ env('CACHE_BUST') }}"></script>
    <link rel="stylesheet" href="/css/lib-dashboard.css?version={{ env('CACHE_BUST') }}" />
    <link rel="stylesheet" href="/css/dashboard.css?version={{ env('CACHE_BUST') }}" />
@endsection

@section('page-top')
    <div class="dashboard-background"></div>
    @include('dashboard.sections.nav')
@endsection

@section('page-bottom')
    @include('dashboard.sections.footer')
@endsection
