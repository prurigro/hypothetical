@extends('dashboard.core')

@section('dashboard-heading')
    @if($export == true)
        <a href="/dashboard/export/{{ $model }}"><button type="button" class="btn btn-default">Export</button></a>
    @endif

    @if($create == true)
        <button type="button" class="new-button btn btn-default">New</button>
    @endif
@endsection

@section('dashboard-body')
    <ul id="edit-list" class="list-group edit-list" data-model="{{ $model }}" {{ $sortcol != false ? "data-sort=$sortcol" : '' }}>
        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}" />

        @foreach($rows as $row)
            <li class="list-group-item" data-id="{{ $row['id'] }}">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-xs-9 title-column">
                            @if($sortcol != false)
                                <i class="fa fa-bars sort-icon" title="Click and drag to reorder"></i>
                            @endif

                            @if(is_array($column))
                                @foreach($column as $col)
                                    <div class="column">{{ $row[$col] }}</div>

                                    @if(!$loop->last)
                                        <div class="column">|</div>
                                    @endif
                                @endforeach
                            @else
                                {{ $row[$column] }}
                            @endif
                        </div>

                        <div class="col-xs-3 button-column">
                            <button type="button" class="edit-button btn btn-warning">Edit</button>

                            @if($delete == true)
                                <button type="button" class="delete-button btn btn-danger">Delete</button>
                            @endif
                        </div>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
@endsection
