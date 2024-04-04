<!DOCTYPE html>
<html lang="{{ Language::getSessionLanguage() }}">
    @php
        // Determine whether the device is mobile
        $device_mobile = !is_null(Request::header('User-Agent')) && (preg_match('/Mobi/', Request::header('User-Agent')) || preg_match('/iP(hone|ad|od);/', Request::header('User-Agent')));

        // If an overridden title has been set (error pages) then use that, otherwise use the Meta model to populate metadata
        if (isset($title)) {
            $meta = [ 'title' => $title . ' - ' . env('APP_NAME'), 'description' => '', 'keywords' => '' ];
        } else {
            $meta = App\Models\Meta::getData(Request::path());
        }
    @endphp

    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="theme-color" content="#fcfcfc" />

        <title>{{ $meta['title'] }}</title>

        <meta name="title" content="{{ $meta['title'] }}" />
        <meta name="description" content="{{ $meta['description'] }}" />
        <meta name="keywords" content="{{ $meta['keywords'] }}" />
        <meta name="dc:title" content="{{ $meta['title'] }}" />
        <meta name="dc:description" content="{{ $meta['description'] }}" />

        <meta property="og:type" content="article" />
        <meta property="og:title" content="{{ $meta['title'] }}" />
        <meta property="og:description" content="{{ $meta['description'] }}" />
        <meta property="og:url" content="{{ Request::url() }}" />
        <meta property="og:image" content="{{ asset('/img/logo.png') }}" />

        <meta name="twitter:card" content="summary" />
        <meta name="twitter:title" content="{{ $meta['title'] }}" />
        <meta name="twitter:description" content="{{ $meta['description'] }}" />
        <meta name="twitter:image" content="{{ asset('/img/logo.png') }}" />

        <link rel="shortcut icon" href="{{ URL::to('/') }}/favicon.ico?version={{ Version::get() }}" />
        <link rel="icon" href="{{ URL::to('/') }}/favicon.ico?version={{ Version::get() }}" type="image/x-icon" />
        <link rel="icon" href="{{ URL::to('/') }}/favicon.png?version={{ Version::get() }}" type="image/png" />
        <link rel="canonical" href="{{ Request::url() }}" />

        @yield('page-includes')
    </head>

    <body class="{{ $device_mobile ? 'mobile-browser' : 'desktop-browser' }}">
        <div class="site-content">
            @yield('page-top')

            <div class="page-content">
                @yield('page-content')
            </div>

            @yield('page-bottom')
        </div>

        @if(Config::get('app.debug') && Config::get('app.env') === 'local')
            <script id="__bs_script__">//<![CDATA[
                document.write("<script async src='http://HOST:3000/browser-sync/browser-sync-client.js?version={{ Version::get() }}'><\/script>".replace("HOST", location.hostname));
            //]]></script>
        @endif
    </body>
</html>
