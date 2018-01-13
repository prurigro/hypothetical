<nav class="navbar navbar-default">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#spark-navbar-collapse">
                <span class="sr-only">Toggle Navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand" href="{{ url('/dashboard') }}">
                {{ env('APP_NAME') }} Dashboard
            </a>
        </div>

        <div id="spark-navbar-collapse" class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                @if (Auth::guest())
                    <li><a href="{{ url('/login') }}">Login</a></li>

                    @if(env('REGISTRATION', false))
                        <li><a href="{{ url('/register') }}">Register</a></li>
                    @endif
                @else
                    @foreach(App\Models\DashboardMenu::$menu as $menu_item)
                        @if(is_array($menu_item[1]))
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                    {{ $menu_item[0] }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu" role="menu">
                                    @foreach($menu_item[1] as $submenu_item)
                                        <li><a href="{{ url('/dashboard/' . $submenu_item[1]) }}">{{ $submenu_item[0] }}</a></li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ url('/dashboard/' . $menu_item[1]) }}">
                                    {{ $menu_item[0] }}
                                </a>
                            </li>
                        @endif
                    @endforeach

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <ul class="dropdown-menu" role="menu">
                            <li><a href="{{ url('/logout') }}">Logout</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>
