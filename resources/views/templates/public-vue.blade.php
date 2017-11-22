@extends('templates.base')

@section('page-includes')
    <script src="/js/lib.js?version={{ env('CACHE_BUST') }}"></script>
    <link rel="stylesheet" href="/css/app.css?version={{ env('CACHE_BUST') }}" />
    @include('elements.variables')
@endsection

@section('page-content')
    <nav-component></nav-component>

    <div class="page-container">
        <div id="router-view" class="main-content">
            <router-view></router-view>
        </div>

        <footer-component></footer-component>
    </div>
@endsection

@section('page-bottom')
    <script src="/js/app-vue.js?version={{ env('CACHE_BUST') }}"></script>
@endsection
