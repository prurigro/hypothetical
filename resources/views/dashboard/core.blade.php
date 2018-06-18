@extends('templates.dashboard')

@section('page-content')
    <div class="container spark-screen">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        {{ $heading }}

                        <div class="dashboard-heading">
                            @yield('dashboard-heading')
                        </div>
                    </div>

                    <div class="card-body dashboard">
                        @yield('dashboard-body')
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmation-modal" class="modal confirmation">
        <div class="card">
            <div class="card-header"></div>
            <button type="button" class="cancel-button btn btn-primary">Cancel</button>
            <button type="button" class="confirm-button btn btn-danger">Confirm</button>
        </div>
    </div>

    <div id="alert-modal" class="modal alert">
        <div class="card">
            <div class="card-header">ALERT</div>
            <div class="message"></div>
            <button type="button" class="accept-button btn btn-primary">OK</button>
        </div>
    </div>
@endsection
