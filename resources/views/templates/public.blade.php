@extends('templates.base')

@section('page-includes')
    <script src="/js/lib.js?version={{ Version::get() }}"></script>
    <link rel="stylesheet" href="/css/app.css?version={{ Version::get() }}" />

    <script>
        var env = {
            appName: "{!! env('APP_NAME') !!}",
            appDesc: "{!! env('APP_DESC') !!}",
            appLang: "{{ Language::getSessionLanguage() }}",
            appDefaultLang: "{{ env('DEFAULT_LANGUAGE') }}",
            apiToken: "{{ Auth::check() ? '?api_token=' . Auth::user()->api_token : '' }}",
            currentUrl: "{{ Request::url() }}",
            csrfToken: "{{ csrf_token() }}",
            debug: {{ Config::get('app.debug') ? 'true' : 'false' }}
        };
    </script>
@endsection

@section('page-content')
    <div id="vue-container">
        <nav-component></nav-component>

        <div class="flex-fix">
            <div class="page-container">
                <div id="router-view" class="main-content">
                    <router-view></router-view>
                </div>

                <footer-component></footer-component>
            </div>
        </div>
    </div>
@endsection

@section('page-bottom')
    <script src="/js/app.js?version={{ Version::get() }}"></script>
@endsection
