@extends('templates.base')

@section('page-includes')
    <script src="/js/lib.js?version={{ env('CACHE_BUST') }}"></script>
    <script src="/js/app.js?version={{ env('CACHE_BUST') }}"></script>
    <link rel="stylesheet" href="/css/app.css?version={{ env('CACHE_BUST') }}" />
@endsection

@section('page-content')
    @include('sections.nav')

    <div class="flex-wrapper">
        <div class="page-container">
            <div class="main-content">
                @yield('content')
            </div>

            @include('sections.footer')
        </div>
    </div>
@endsection
