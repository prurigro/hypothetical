@extends('dashboard.core', [
    'heading' => 'Dashboard Home'
])

@section('dashboard-body')
    <div class="list-group menu-list">
        @foreach(App\Dashboard::$menu as $menu_item)
            @if(array_key_exists('submenu', $menu_item))
                @foreach($menu_item['submenu'] as $submenu_item)
                    <li class="list-group-item">
                        {{ $menu_item['title'] }}: {{ $submenu_item['title'] }}

                        <a
                            class="list-group-item-link"
                            href="/dashboard/{{ $submenu_item['type'] }}/{{ $submenu_item['model'] }}">
                        </a>
                    </li>
                @endforeach
            @else
                <li class="list-group-item">
                    {{ $menu_item['title'] }}

                    <a
                        class="list-group-item-link"
                        href="/dashboard/{{ $menu_item['type'] }}/{{ $menu_item['model'] }}">
                    </a>
                </li>
            @endif
        @endforeach
    </div>
@endsection
