@extends('base')

@section('page-content')
<div class="container auth-container">
    <div class="row">
        <div class="col-xs-6 col-xs-push-3">
            @yield('auth-form')
        </div>
    </div>
</div>
@endsection
