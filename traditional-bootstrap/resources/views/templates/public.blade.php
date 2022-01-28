@extends('templates.base')

@section('page-includes')
    <script src="/js/lib.js?version={{ Version::get() }}"></script>
    <script src="/js/app.js?version={{ Version::get() }}"></script>
    <link rel="stylesheet" href="/css/app.css?version={{ Version::get() }}" />
@endsection

@section('page-content')
    @include('sections.nav')

    <div class="flex-fix">
        <div class="page-container">
            <div class="main-content">
                @yield('content')
            </div>

            @include('sections.footer')
        </div>
    </div>
@endsection
