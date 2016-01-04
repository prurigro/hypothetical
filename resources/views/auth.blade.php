@extends('base')

@section('page-content')
    <div class="container auth-container">
        <div class="row">
            <div class="col-xs-12 col-md-6 col-md-offset-3">
                @yield('auth-form')
            </div>
        </div>
    </div>
@endsection
