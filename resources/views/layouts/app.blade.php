<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PhotoG') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .modal-backdrop {
            display: none !important;
        }
    </style>
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light fixed-top">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ url('/') }}">
                    Photo<span>G</span>
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>
                    @guest
                    @else
                        <top-middle-field></top-middle-field>
                    @endguest
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xl-2 col-lg-2 col-md-2 m-0 p-0">
                    @guest

                    @else
                        <div class="menu-items">
                            <div class="searchbar">
                                <input id="search_input" class="search_input" type="text" name="search" placeholder="Search...">
                                <a href="#" class="search_icon" id="search_icon"><i class="fas fa-search"></i></a>
                            </div>
                            <div class="left-menu-item">
                                <a href="{{ route('home') }}" class="{{ Request::is('home') ? 'now-on' : '' }}"><i class="fas fa-home"></i><span class="left-menu-text">{{ __('Home') }}</span></a>
                            </div>
                            <hr>
                            <div class="left-menu-item">
                                <a href="{{ route('show', ['tag' => Carbon\Carbon::now()->year ]) }}" class=""><i class="fas fa-calendar-day"></i><span class="left-menu-text">{{ __('This year') }}</span></a>
                            </div>
                            <div class="left-menu-item">
                                <a href="{{ route('albums') }}" class="{{ Request::is('albums') ? 'now-on' : '' }}"><i class="fas fa-history"></i><span class="left-menu-text">{{ __('Albums') }}</span></a>
                            </div>
                            <div class="left-menu-item">
                                <a href="#" class=""><i class="fas fa-hourglass-start"></i><span class="left-menu-text">{{ __('Temp links') }}</span></a>
                            </div>
                            <div class="left-menu-item">
                                <a href="#" class=""><i class="fas fa-tag tag"></i><span class="left-menu-text">{{ __('Tags') }}</span></a>
                            </div>
                            <div class="left-menu-item">
                                <a href="#" class=""><i class="fas fa-users"></i><span class="left-menu-text">{{ __('Users') }}</span></a>
                            </div>
                            <div class="left-menu-item">
                                <a href="#" class=""><i class="fas fa-user-tag"></i><span class="left-menu-text">{{ __('Permissions') }}</span></a>
                            </div>
                            <hr>
                            <div class="left-menu-item">
                                <a href="#" class=""><i class="fas fa-sign-out-alt"></i><span class="left-menu-text">{{ __('Logout') }}</span></a>
                            </div>
                        </div>
                    @endguest
                    </div>
                    <div class="col-xl-8 col-lg-8 col-md-8">
                        @yield('content')
                    </div>
                    <div class="col-xl-2 col-lg-2 col-md-2 m-0 p-0">
                    @guest

                    @else
                        <div class="tag-menu">
                            <div class="tag-menu-title">Tags</div>
                            @foreach($tags as $tag)
                                <div class="tag-menu-item">
                                    <a href="{{ route('show', ['tag' => $tag->tag ]) }}"><i class="fas fa-tag tag"></i>{{ $tag->tag }}</a>
                                </div>
                            @endforeach
                        </div>
                    @endguest

                    </div>
                </div>
            </div>
        </main>
        <footer>
            <a href="http://www.darmis.lt" target="_blank">dar-mis</a>
        </footer>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            $('#search_input').keydown(function(e) {
                if (e.which === 13) {
                    var url = "{{ route('show', ['tag' => 'destination' ]) }}";
                    url = url.replace('destination', e.currentTarget.value);
                    window.location = url;
                    // console.log(e);
                }
            })
            $('#search_icon').click(function(e) {
                    var url = "{{ route('show', ['tag' => 'destination' ]) }}";
                    url = url.replace('destination', e.currentTarget.previousElementSibling.value);
                    window.location = url;
            })
        });
    </script>
</body>
</html>
