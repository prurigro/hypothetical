@extends('dashboard.core')

@section('dashboard-heading')
    @if($export && count($rows) > 0)
        <a href="/dashboard/export/{{ $model }}"><button type="button" class="btn btn-secondary">Export</button></a>
    @endif

    @if($create)
        <button type="button" class="new-button btn btn-secondary">New</button>
    @endif
@endsection

@section('dashboard-body')
    <div id="edit-list-wrapper">
        <input type="hidden" id="token" value="{{ csrf_token() }}" />

        @if($filter)
            <input id="filter-input" class="search" placeholder="Filter" />
        @endif

        <ul id="edit-list" class="list-group edit-list list" data-model="{{ $model }}" {{ $sortcol != false ? "data-sort=$sortcol" : '' }}>
            @foreach($rows as $row)
                <li class="list-group-item {{ $sortcol != false ? 'sortable' : '' }}" data-id="{{ $row['id'] }}">
                    <div class="title-column">
                        @if($sortcol != false)
                            <div class="sort-icon" title="Click and drag to reorder">
                                <div class="sort-icon-inner">
                                    <div class="sort-icon-inner-bar"></div>
                                    <div class="sort-icon-inner-bar"></div>
                                    <div class="sort-icon-inner-bar"></div>
                                </div>
                            </div>
                        @endif

                        @foreach($display as $display_column)
                            @if($row[$display_column] != '')
                                <div class="column">{{ $row[$display_column] }}</div>

                                @if(!$loop->last)
                                    <div class="spacer">|</div>
                                @endif
                            @endif
                        @endforeach
                    </div>

                    <div class="button-column">
                        @if(!empty($button))
                            <button type="button" class="action-button btn btn-secondary" data-confirmation="{{ $button[1] }}" data-success="{{ $button[2] }}" data-error="{{ $button[3] }}" data-url="{{ $button[4] }}">{{ $button[0] }}</button>
                        @endif

                        <a class="edit-button btn btn-warning" href="/dashboard/edit/{{ $model }}/{{ $row['id'] }}">Edit</a>

                        @if($delete)
                            <button type="button" class="delete-button btn btn-danger">Delete</button>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
@endsection
