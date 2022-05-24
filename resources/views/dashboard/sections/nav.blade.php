<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="/dashboard">
            {{ env('APP_NAME') }} Dashboard
        </a>

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#dashboard-navbar"
            aria-controls="dashboard-navbar"
            aria-expanded="false"
            aria-label="Toggle navigation">

            <span class="navbar-toggler-icon">
                <span class="navbar-toggler-icon-bar"></span>
                <span class="navbar-toggler-icon-bar"></span>
                <span class="navbar-toggler-icon-bar"></span>
            </span>
        </button>

        <div id="dashboard-navbar" class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                @if (Auth::guest())
                    <li class="nav-item"><a class="nav-link {{ $current_page == 'login' ? 'active' : '' }}" href="/login">Login</a></li>

                    @if(App\Dashboard::canRegister())
                        <li class="nav-item"><a class="nav-link {{ $current_page == 'register' ? 'active' : '' }}" href="/register">Register</a></li>
                    @endif
                @else
                    @foreach(App\Dashboard::$menu as $menu_item)
                        @if(array_key_exists('submenu', $menu_item))
                            @php
                                $dropdown_id = preg_replace([ '/\ \ */', '/[^a-z\-]/' ], [ '-', '' ], strtolower($menu_item['title']));
                            @endphp

                            <li class="nav-item dropdown">
                                <button
                                    id="menu-dropdown-{{ $dropdown_id }}"
                                    type="button"
                                    class="nav-link dropdown-toggle {{ array_search($current_page, array_column($menu_item['submenu'], 'model')) !== false ? 'active' : '' }}"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">

                                    {{ $menu_item['title'] }} <span class="caret"></span>
                                </button>

                                <ul class="dropdown-menu" aria-labelledby="menu-dropdown-{{ $dropdown_id }}">
                                    @foreach($menu_item['submenu'] as $submenu_item)
                                        <li><a class="dropdown-item {{ $current_page == $submenu_item['model'] ? 'active' : '' }}" href="/dashboard/{{ $submenu_item['type'] }}/{{ $submenu_item['model'] }}">{{ $submenu_item['title'] }}</a></li>
                                    @endforeach
                                </ul>
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
                        <button
                            id="user-dropdown"
                            type="button"
                            class="nav-link dropdown-toggle {{ $current_page == 'settings' ? 'active' : '' }}"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">

                            {{ Auth::user()->name }} <span class="caret"></span>
                        </button>

                        <ul class="dropdown-menu" aria-labelledby="user-dropdown">
                            <li><a class="dropdown-item {{ $current_page == 'settings' ? 'active' : '' }}" href="/dashboard/settings">Settings</a></li>
                            <li><a class="dropdown-item" href="/logout">Logout</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
