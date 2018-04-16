@extends('templates.dashboard')

@section('page-content')
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header">Reset Password</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form role="form" method="POST" action="{{ url('/password/email') }}">
                            {!! csrf_field() !!}

                            <div class="form-group row {{ $errors->has('email') ? 'has-error' : '' }}">
                                <label class="col-12 col-md-4 col-form-label">E-Mail Address</label>

                                <div class="col-12 col-md-6">
                                    <input class="form-control" type="email" name="email" value="{{ old('email') }}" />

                                    @if ($errors->has('email'))
                                        <span class="text-muted">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-12 col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Send Password Reset Link
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
