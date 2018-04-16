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

    <div id="confirmation-modal" class="modal">
        <div class="modal-container">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                        <div class="card">
                            <div class="card-header"></div>
                            <button type="button" class="cancel-button btn btn-primary">Cancel</button>
                            <button type="button" class="confirm-button btn btn-danger">Confirm</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="alert-modal" class="modal">
        <div class="modal-container">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-6 offset-md-3 col-lg-4 offset-lg-4">
                        <div class="card">
                            <div class="card-header">ALERT</div>
                            <div class="message"></div>
                            <button type="button" class="accept-button btn btn-primary">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
