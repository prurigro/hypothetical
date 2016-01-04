@extends('auth')

@section('auth-form')
    <form method="POST" action="/auth/register">
        {!! csrf_field() !!}

        <div class="form-field">
            <label for="name">Name</label>
            <div class="input"><input type="text" name="name" value="{{ old('name') }}" /></div>
        </div>

        <div class="form-field">
            <label for="email">Email</label>
            <div class="input"><input type="email" name="email" value="{{ old('email') }}" /></div>
        </div>

        <div class="form-field">
            <label for="password">Password</label>
            <div class="input"><input type="password" name="password" id="password" /></div>
        </div>

        <div class="form-field">
            <label for="password_confirmation">Confirm</label>
            <div class="input"><input type="password" name="password_confirmation" id="password_confirmation" /></div>
        </div>

        <button type="submit">Register</button>
    </form>
@endsection
