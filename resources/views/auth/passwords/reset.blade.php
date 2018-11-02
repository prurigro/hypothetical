@extends('templates.dashboard')

@section('page-content')
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header">Reset Password</div>

                    <div class="card-body">
                        <form role="form" method="POST" action="{{ url('/password/reset') }}">
                            {!! csrf_field() !!}

                            <input type="hidden" name="token" value="{{ $token }}" />

                            <div class="form-group row {{ $errors->has('email') ? 'has-error' : '' }}">
                                <label class="col-12 col-md-4 col-form-label">E-Mail Address</label>

                                <div class="col-12 col-md-6">
                                    <input class="form-control" type="email" name="email" value="{{ $email ?? old('email') }}" />

                                    @if ($errors->has('email'))
                                        <span class="text-muted">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row {{ $errors->has('password') ? 'has-error' : '' }}">
                                <label class="col-12 col-md-4 col-form-label">Password</label>

                                <div class="col-12 col-md-6">
                                    <input class="form-control" type="password" name="password" />

                                    @if ($errors->has('password'))
                                        <span class="text-muted">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
                                <label class="col-12 col-md-4 col-form-label">Confirm Password</label>

                                <div class="col-12 col-md-6">
                                    <input class="form-control" type="password" name="password_confirmation" />

                                    @if ($errors->has('password_confirmation'))
                                        <span class="text-muted">
                                            <strong>{{ $errors->first('password_confirmation') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-12 col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        Reset Password
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
