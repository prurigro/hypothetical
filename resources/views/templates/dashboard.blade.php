@extends('templates.base', [ 'title' => 'Dashboard' ])
@set('current_page', preg_replace([ '/https?:\/\/[^\/]*\/dashboard\/[^\/]*\//', '/\/.*/' ], [ '', '' ],  Request::url()))

@section('page-includes')
    <script src="/js/lib-dashboard.js?version={{ Version::get() }}"></script>
    <script src="/js/dashboard.js?version={{ Version::get() }}"></script>
    <link rel="stylesheet" href="/css/lib-dashboard.css?version={{ Version::get() }}" />
    <link rel="stylesheet" href="/css/dashboard.css?version={{ Version::get() }}" />
@endsection

@section('page-top')
    <div class="dashboard-background"></div>
    @include('dashboard.sections.nav')
@endsection

@section('page-bottom')
    @include('dashboard.sections.footer')
@endsection
