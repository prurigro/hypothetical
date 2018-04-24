@extends('dashboard.core', [
    'heading' => 'Credits'
])

@section('dashboard-body')
    <div class="dashboard-credits">
        <h2>Authors</h2>

        <ul>
            @foreach(App\Dashboard::$author_credits as $credit)
                <li><a href="{{ $credit['url'] }}" target="_blank" rel="noreferrer">{{ $credit['name'] }}</a></li>
            @endforeach
        </ul>

        <h2>Libraries</h2>

        <ul>
            @foreach(App\Dashboard::$library_credits as $credit)
                <li><a href="{{ $credit['url'] }}" target="_blank" rel="noreferrer">{{ $credit['name'] }}</a></li>
            @endforeach
        </ul>
    </div>
@endsection
