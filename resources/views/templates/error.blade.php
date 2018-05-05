@extends('templates.base')

@section('page-includes')
    <link rel="stylesheet" href="/css/error.css?version={{ env('CACHE_BUST') }}" />
@endsection

@section('page-content')
    <div class="flex-fix">
        <div class="error-page">
            <div class="error-page-content">
                {{ $title }}
            </div>
        </div>
    </div>
@endsection
