@extends('layouts.public', [ 'title' => 'Home' ])

@section('content')
    <div id="subscription-form">
        <form action="#" method="POST" accept-charset="UTF-8">
            <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}" />
            <input type="text" name="email" id="email" placeholder="Email" />
            <input type="text" name="name" id="name" placeholder="Name" />
            <input type="text" name="address" id="address" placeholder="Postal / ZIP" />
            <div id="notification"></div>
            <input id="submit" type="submit" value="Subscribe" />
        </form>
    </div>
@endsection
