@extends('dashboard.core')

@section('dashboard-body')
    @set('menu_class', 'list-group-item')
    <ul class="list-group linked-list">@include('dashboard.sections.menu')</ul>
@endsection
