@extends('templates.public')

@section('content')
    <div class="contact-page-component">
        <div class="container">
            <div class="row">
                <div class="col col-md-8 offset-md-2">
                    <h1>Contact</h1>
                </div>
            </div>

            <div class="row">
                <div class="col col-md-8 offset-md-2">
                    <form action="#" method="POST" accept-charset="UTF-8">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                        <input type="text" name="name" placeholder="Name" />
                        <input type="text" name="email" placeholder="Email" />
                        <textarea name="message" placeholder="Message"></textarea>
                        <input class="submit" type="submit" name="submit" value="Submit" />
                    </form>

                    <div class="notification"><strong>Error:</strong> There were problems with the <span>0</span> fields highlighted above</div>
                </div>
            </div>
        </div>
    </div>
@endsection
