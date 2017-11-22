@extends('templates.dashboard')

@section('page-content')
    <div class="container spark-screen">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        {{ $heading }}

                        <div class="dashboard-heading">
                            @yield('dashboard-heading')
                        </div>
                    </div>

                    <div class="panel-body">
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
                    <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                        <div class="panel panel-default">
                            <div class="panel-heading"></div>
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
                    <div class="col-xs-12 col-sm-6 col-sm-offset-3 col-md-4 col-md-offset-4">
                        <div class="panel panel-default">
                            <div class="panel-heading">ALERT</div>
                            <div class="message"></div>
                            <button type="button" class="accept-button btn btn-primary">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
