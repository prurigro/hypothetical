@extends('dashboard.core')

@section('dashboard-heading')
    @if($export)
        <a href="/dashboard/export/{{ $model }}"><button type="button" class="btn btn-default">Export</button></a>
    @endif

    @if($create)
        <button type="button" class="new-button btn btn-default">New</button>
    @endif
@endsection

@section('dashboard-body')
    <div id="edit-list-wrapper">
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}" />

        @if($filter)
            <input id="filter-input" class="search" placeholder="Filter" />
        @endif

        <ul id="edit-list" class="list-group edit-list list" data-model="{{ $model }}" data-path="{{ isset($path) ? $path : $model }}" {{ $sortcol != false ? "data-sort=$sortcol" : '' }}>
            @foreach($rows as $row)
                <li class="list-group-item" data-id="{{ $row['id'] }}">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="title-column">
                                @if($sortcol != false)
                                    <div class="sort-icon">
                                        <i class="fa fa-bars" title="Click and drag to reorder"></i>
                                    </div>
                                @endif

                                @if(is_array($column))
                                    @foreach($column as $col)
                                        @if($row[$col] != '')
                                            <div class="column">{{ $row[$col] }}</div>

                                            @if(!$loop->last)
                                                <div class="spacer">|</div>
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    {{ $row[$column] }}
                                @endif
                            </div>

                            <div class="button-column">
                                @if(isset($button) && is_array($button))
                                    <button type="button" class="action-button btn btn-default" data-confirmation="{{ $button[1] }}" data-success="{{ $button[2] }}" data-error="{{ $button[3] }}" data-url="{{ $button[4] }}">{{ $button[0] }}</button>
                                @endif

                                <button type="button" class="edit-button btn btn-warning">Edit</button>

                                @if($delete)
                                    <button type="button" class="delete-button btn btn-danger">Delete</button>
                                @endif
                            </div>
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
