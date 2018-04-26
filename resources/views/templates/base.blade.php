<!DOCTYPE html>
<html lang="{{ Language::getSessionLanguage() }}">
    @set('page_title', (isset($title) ? $title . ' - ' : '') . env('APP_NAME'))
    @set('device_mobile', preg_match('/Mobi/', Request::header('User-Agent')) || preg_match('/iP(hone|ad|od);/', Request::header('User-Agent')))

    <head>
        <title>{{ $page_title }}</title>

        <meta charset="UTF-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="theme-color" content="#fcfcfc" />

        <meta name="title" content="{{ $page_title }}" />
        <meta name="description" content="{{ env('APP_DESC') }}" />
        <meta name="dc:title" content="{{ $page_title }}" />
        <meta name="dc:description" content="{{ env('APP_DESC') }}" />

        <meta property="og:type" content="article" />
        <meta property="og:title" content="{{ $page_title }}" />
        <meta property="og:description" content="{{ env('APP_DESC') }}" />
        <meta property="og:url" content="{{ Request::url() }}" />
        <meta property="og:image" content="{{ asset('/img/logo.png') }}" />

        <meta name="twitter:card" content="summary" />
        <meta name="twitter:title" content="{{ $page_title }}" />
        <meta name="twitter:description" content="{{ env('APP_DESC') }}" />
        <meta name="twitter:image" content="{{ asset('/img/logo.png') }}" />

        <link rel="shortcut icon" href="{{ URL::to('/') }}/favicon.ico?version={{ env('CACHE_BUST') }}" />
        <link rel="icon" href="{{ URL::to('/') }}/favicon.ico?version={{ env('CACHE_BUST') }}" type="image/x-icon" />
        <link rel="icon" href="{{ URL::to('/') }}/favicon.png?version={{ env('CACHE_BUST') }}" type="image/png" />
        <link rel="canonical" href="{{ Request::url() }}" />

        @yield('page-includes')
    </head>

    <body class="{{ $device_mobile ? 'mobile-browser' : 'desktop-browser' }}">
        <div class="flex-wrapper">
            <div class="site-content">
                @yield('page-top')

                <div class="page-content">
                    @yield('page-content')
                </div>

                @yield('page-bottom')
            </div>
        </div>

        @if(Config::get('app.debug') && Config::get('app.env') === 'local')
            <script id="__bs_script__">//<![CDATA[
                document.write("<script async src='http://{{ env('BS_HOST', 'localhost') }}:3000/browser-sync/browser-sync-client.js?version={{ env('CACHE_BUST') }}'><\/script>".replace("HOST", location.hostname));
            //]]></script>
        @endif
    </body>
</html>
