@extends('templates.dashboard')

@section('page-content')
    <div class="container spark-screen">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        Credits
                    </div>

                    <div class="card-body dashboard">
                        <div class="dashboard-credits">
                            <h2>Authors</h2>

                            <ul>
                                @foreach(App\Models\Dashboard::$author_credits as $credit)
                                    <li><a href="{{ $credit['url'] }}" target="_blank" rel="noreferrer">{{ $credit['name'] }}</a></li>
                                @endforeach
                            </ul>

                            <h2>Libraries</h2>

                            <ul>
                                @foreach(App\Models\Dashboard::$library_credits as $credit)
                                    <li><a href="{{ $credit['url'] }}" target="_blank" rel="noreferrer">{{ $credit['name'] }}</a></li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
