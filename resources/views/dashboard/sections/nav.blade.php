<nav class="navbar navbar-expand-lg">
    <a class="navbar-brand" href="/dashboard">
        {{ env('APP_NAME') }} Dashboard
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#dashboard-navbar" aria-controls="dashboard-navbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon">
            <span class="navbar-toggler-icon-bar"></span>
            <span class="navbar-toggler-icon-bar"></span>
            <span class="navbar-toggler-icon-bar"></span>
        </span>
    </button>

    <div id="dashboard-navbar" class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            @if (Auth::guest())
                <li class="nav-item"><a class="nav-link {{ $current_page == 'login' ? 'active' : '' }}" href="/login">Login</a></li>

                @if(App\Dashboard::canRegister())
                    <li class="nav-item"><a class="nav-link {{ $current_page == 'register' ? 'active' : '' }}" href="/register">Register</a></li>
                @endif
            @else
                @foreach(App\Dashboard::$menu as $menu_item)
                    @if(array_key_exists('submenu', $menu_item))
                        @set('dropdown_id', preg_replace([ '/\ \ */', '/[^a-z\-]/' ], [ '-', '' ], strtolower($menu_item['title'])))

                        <li class="nav-item dropdown">
                            <span id="menu-dropdown-{{ $dropdown_id }}" class="nav-link dropdown-toggle {{ array_search($current_page, array_column($menu_item['submenu'], 'model')) !== false ? 'active' : '' }}" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ $menu_item['title'] }} <span class="caret"></span>
                            </span>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="menu-dropdown-{{ $dropdown_id }}">
                                @foreach($menu_item['submenu'] as $submenu_item)
                                    <a class="dropdown-item {{ $current_page == $submenu_item['model'] ? 'active' : '' }}" href="/dashboard/{{ $submenu_item['type'] }}/{{ $submenu_item['model'] }}">{{ $submenu_item['title'] }}</a>
                                @endforeach
                            </div>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ $current_page == $menu_item['model'] ? 'active' : '' }}" href="/dashboard/{{ $menu_item['type'] }}/{{ $menu_item['model'] }}">
                                {{ $menu_item['title'] }}
                            </a>
                        </li>
                    @endif
                @endforeach

                <li class="nav-item dropdown">
                    <a id="user-dropdown" class="nav-link dropdown-toggle {{ $current_page == 'settings' ? 'active' : '' }}" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-dropdown">
                        <a class="dropdown-item {{ $current_page == 'settings' ? 'active' : '' }}" href="/dashboard/settings">Settings</a>
                        <a class="dropdown-item" href="/logout">Logout</a>
                    </div>
                </li>
            @endif
        </ul>
    </div>
</nav>
