@extends('dashboard.core')

@section('dashboard-heading')
    <button type="button" class="new-button btn btn-default">New</button>
@endsection

@section('dashboard-body')
    @set('sort_data', $sortcol != false ? "data-sort=$sortcol" : '')
    @set('sort_icon', $sortcol != false ? '<i class="fa fa-bars sort-icon" title="Click and drag to reorder"></i>' : '')

    <ul id="edit-list" class="list-group edit-list" data-model="{{ $model }}" {{ $sort_data }}>
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}" />

        @foreach($rows as $row)
            <li class="list-group-item" data-id="{{ $row['id'] }}">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-9 title-column">
                            {!! $sort_icon !!}
                            {{ $row[$column] }}
                        </div>

                        <div class="col-xs-3 button-column">
                            <button type="button" class="edit-button btn btn-warning">Edit</button>
                            <button type="button" class="delete-button btn btn-danger">Delete</button>
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@endsection
