@extends('templates.dashboard')

@section('page-content')
    <div class="container">
        <div class="row">
            <div class="col-12 col-lg-8 offset-lg-2">
                <div class="card">
                    <div class="card-header">Login</div>

                    <div class="card-body">
                        <form role="form" method="POST" action="{{ url('/login') }}">
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

                            <div class="form-group row">
                                <div class="col-12 col-md-6 offset-md-4">
                                    <label class="checkbox">
                                        <input type="checkbox" class="styled" id="remember" name="remember" />
                                        <span></span>
                                        Remember Me
                                    </label>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col-12 col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary vertical-margin">
                                        Login
                                    </button>

                                    <a class="btn btn-link vertical-margin" href="{{ url('/password/reset') }}">Forgot Your Password?</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
