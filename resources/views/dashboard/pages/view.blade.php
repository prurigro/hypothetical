@extends('dashboard.core')

@section('dashboard-heading')
    @if(count($rows) > 0)
        <a href="/dashboard/export/{{ $model }}"><button type="button" class="btn btn-secondary">Export</button></a>
    @endif
@endsection

@section('dashboard-body')
    <div class="view-table-container">
        <table class="table">
            <thead>
                <tr class="heading-row">
                    @foreach($columns as $index => $column)
                        <th>{{ $column_headings[$index] }}</th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($columns as $index => $column)
                            <td><strong class="mobile-heading">{{ $column_headings[$index] }}: </strong>{{ $row[$column['name']] }}</td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
