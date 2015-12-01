@extends('auth')

@section('auth-form')
<form method="POST" action="/auth/login">
    {!! csrf_field() !!}

    <div class="form-field">
        <label for="email">Email</label>
        <div class="input"><input type="email" name="email" value="{{ old('email') }}" /></div>
    </div>

    <div class="form-field">
        <label for="password">Password</label>
        <div class="input"><input type="password" name="password" id="password" /></div>
    </div>

    <div class="form-field">
        <label for="remember">Remember Me</label>
        <input type="checkbox" name="remember" />
    </div>

    <button type="submit">Login</button>
</form>
@endsection
