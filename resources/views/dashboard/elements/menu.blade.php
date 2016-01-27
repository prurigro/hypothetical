@set('menu', [
    [ 'Contact', 'contact' ],
    [ 'Subscriptions', 'subscriptions' ],
    [ 'Shows', 'shows' ],
    [ 'News', 'news' ],
    [ 'Videos', 'videos' ]
])

@foreach($menu as $menu_item)
    <li class="{{ $menu_class }}"><a href="{{ url('/dashboard/' . $menu_item[1]) }}">{{ $menu_item[0] }}</a></li>
@endforeach
