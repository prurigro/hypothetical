@extends('layouts.base')

@section('page-includes')
    <script src="/js/lib-dashboard.js"></script>
    <script src="/js/dashboard.js"></script>
    <link rel="stylesheet" href="/css/dashboard.css" />
@endsection

@section('page-top')
    @include('dashboard.elements.nav')
@endsection
