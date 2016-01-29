@extends('layouts.public')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">
                <h1>Contact</h1>
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-8 col-md-offset-2">
                <div id="contact-form">
                    <form action="#" method="POST" accept-charset="UTF-8">
                        <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}" />
                        <input type="text" name="name" id="name" placeholder="Name" />
                        <input type="text" name="email" id="email" placeholder="Email" />
                        <textarea name="message" id="message" placeholder="Message"></textarea>

                        <input id="submit" type="submit" value="Submit" />
                    </form>

                    <div id="notification"><strong>Error:</strong> There were problems with the <span>0</span> fields highlighted above</div>
                </div>
            </div>
        </div>
    </div>
@endsection
