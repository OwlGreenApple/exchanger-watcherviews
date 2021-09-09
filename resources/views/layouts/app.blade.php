<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('assets/js/app.js') }}" defer></script>
    <script src="{{ asset('assets/js/jquery-3.2.1.min.js') }}"></script>

    <!-- Styles -->
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
    <!-- Custom select -->
    <link href="{{ asset('assets/css/custom-select.css') }}" rel="stylesheet" />

     <!-- Font Awesome 5 -->
    <link href="{{ asset('assets/font-awesome-5/all.css') }}" rel="stylesheet">

    <!-- Intl Dialing Code -->
    <link href="{{ asset('assets/intl-tel-input/css/intlTelInput.min.css') }}" rel="stylesheet" />
    <script type="text/javascript" src="{{ asset('assets/intl-tel-input/js/intlTelInput.js') }}"></script> 

    <!-- Data Table -->
    <link href="{{ asset('assets/DataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/DataTables/Responsive/css/responsive.dataTables.min.css') }}" rel="stylesheet">

    <script defer type="text/javascript" src="{{ asset('assets/DataTables/datatables.min.js') }}"></script>
    <script defer type="text/javascript" src="{{ asset('assets/DataTables/Responsive/js/dataTables.responsive.min.js') }}"></script>

     <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/order.css') }}" rel="stylesheet" />

    <!-- Canvas JS -->
    <script type="text/javascript" src="{{ asset('assets/canvasjs/canvasjs.min.js') }}"></script>

    <!-- ReactJS -->
    <script type="text/javascript" src="{{ asset('assets/reactjs/babel.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/reactjs/react.production.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('assets/reactjs/react-dom.production.min.js') }}"></script>
</head>
<body>

    <!--Loading Bar-->
    <div class="div-loading">
      <div id="loader" style="display: none;"></div>  
    </div> 

    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('home')}}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <!-- USER -->
                            @if (Auth::user()->is_admin == 0)
                                <!-- <li class="nav-item">
                                    <a class="nav-link" href="{{ url('home') }}">{{ Lang::get('transaction.home') }}</a>
                                </li> -->
                                <li class="nav-item">
                                    <li class="nav-link"><a href="{{ url('trade') }}">{{ Lang::get('transaction.trade') }}</a>&nbsp;<span class="border border-success px-1">{{ Lang::get('custom.currency') }} {{ Price::get_rate() }}/coin</span></li>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('buy') }}">Beli Koin</a>
                                </li>
                               
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('sell') }}">{{ Lang::get('transaction.sell') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('wallet') }}">Wallet</a>
                                </li>
                            @else
                            <!-- ADMIN -->
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('kurs-admin') }}">Kurs Coin</a>
                                </li> 
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('user-list') }}">User List</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('order-list') }}">Order List</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('wa-message') }}">WA Message</a>
                                </li>
                            @endif

                            <li class="nav-item">
                                    <a class="nav-link">{{ Auth::user()->name }}&nbsp;<span><small class="text-success">({{ Auth::user()->membership }})</small></span></a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('account') }}">Akun</a>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
