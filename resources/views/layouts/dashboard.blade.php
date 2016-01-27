@extends('layouts.base')

@section('page-includes')
    <script src="{{ elixir('js/lib-dashboard.js') }}"></script>
    <script src="{{ elixir('js/dashboard.js') }}"></script>
    <link rel="stylesheet" href="{{ elixir('css/dashboard.css') }}" />
@endsection

@section('page-top')
    @include('dashboard.elements.nav')
@endsection
