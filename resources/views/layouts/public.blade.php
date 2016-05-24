@extends('layouts.base')

@section('page-includes')
    <script src="/js/modernizr.js"></script>
    <script src="/js/lib.js"></script>
    <script src="/js/app.js"></script>
    <link rel="stylesheet" href="/css/app.css" />
@endsection

@section('page-top')
    @include('elements.nav')
@endsection

@section('page-bottom')
    @include('elements.footer')
@endsection
