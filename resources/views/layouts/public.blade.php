@extends('layouts.base')

@section('page-includes')
    <script src="{{ elixir('js/app.js') }}"></script>
    <link rel="stylesheet" href="{{ elixir('css/app.css') }}" />
@endsection

@section('page-top')
    @include('elements.nav')
@endsection

@section('page-bottom')
    @include('elements.footer')
@endsection
