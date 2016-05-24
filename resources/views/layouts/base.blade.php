<!DOCTYPE html>
<html lang="en">
    <head>
        {!! Head::render() !!}
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        <script src"/js/modernizr.js"></script>
        @yield('page-includes')

        @if(Config::get('app.debug'))
            <script type="text/javascript">
                document.write('<script src="//{{ env('LR_HOST', 'localhost') }}:35729/livereload.js?snipver=1" type="text/javascript"><\/script>')
            </script>
        @endif
    </head>

    <body class="{{ Request::path() == '/' ? 'index' : preg_replace('/\/.*/', '', Request::path()) }}">
        @yield('page-top')
        <div id="page-content">@yield('content')</div>
        @yield('page-bottom')
    </body>
</html>
