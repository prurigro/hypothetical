@extends('dashboard.core')

@section('dashboard-heading')
    <a href="/dashboard/export/{{ $model }}"><button type="button" class="btn btn-default">Export</button></a>
@endsection

@section('dashboard-body')
    <table class="table">
        <thead>
            <tr class="heading-row">
                @foreach($cols as $column)
                    <th>{{ $column[0] }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @foreach($rows as $row)
                <tr>
                    @foreach($cols as $column)
                        <td><strong class="mobile-heading">{{ $column[0] }}: </strong>{{ $row[$column[1]] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
