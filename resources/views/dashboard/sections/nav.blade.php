<nav class="navbar navbar-expand-lg navbar-dark">
    @set('current_page', preg_replace([ '/^.*\/dashboard\/?/', '/\/.*/' ], [ '', '' ], Request::url()))

    <a class="navbar-brand" href="{{ url('/dashboard') }}">
        {{ env('APP_NAME') }} Dashboard
    </a>

    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#dashboard-navbar" aria-controls="dashboard-navbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div id="dashboard-navbar" class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto">
            @if (Auth::guest())
                <li class="nav-item"><a class="nav-link" href="{{ url('/login') }}">Login</a></li>

                @if(env('REGISTRATION', false))
                    <li class="nav-item"><a class="nav-link" href="{{ url('/register') }}">Register</a></li>
                @endif
            @else
                @foreach(App\Models\DashboardMenu::$menu as $menu_item)
                    @if(array_key_exists('submenu', $menu_item))
                        <li class="nav-item dropdown">
                            <a id="menu-dropdown" class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ $menu_item['title'] }} <span class="caret"></span>
                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="menu-dropdown">
                                @foreach($menu_item['submenu'] as $submenu_item)
                                    <a class="dropdown-item" href="{{ url('/dashboard/' . $submenu_item['type'] . '/' . $submenu_item['model']) }}">{{ $submenu_item['title'] }}</a>
                                @endforeach
                            </div>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ $current_page == $menu_item['model'] ? 'active' : '' }}" href="{{ url('/dashboard/' . $menu_item['type'] . '/' . $menu_item['model']) }}">
                                {{ $menu_item['title'] }}
                            </a>
                        </li>
                    @endif
                @endforeach

                <li class="nav-item dropdown">
                    <a id="user-dropdown" class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        {{ Auth::user()->name }} <span class="caret"></span>
                    </a>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="user-dropdown">
                        <a class="dropdown-item" href="{{ url('/logout') }}">Logout</a>
                    </div>
                </li>
            @endif
        </ul>
    </div>
</nav>
