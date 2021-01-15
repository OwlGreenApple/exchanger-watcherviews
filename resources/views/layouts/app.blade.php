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

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <!-- Data Table -->
    <link href="{{ asset('/assets/DataTables/datatables.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/assets/DataTables/Responsive/css/responsive.dataTables.min.css') }}" rel="stylesheet">
    <script defer type="text/javascript" src="{{ asset('/assets/DataTables/datatables.min.js') }}"></script>
    <script defer type="text/javascript" src="{{ asset('/assets/DataTables/Responsive/js/dataTables.responsive.min.js') }}"></script> 

    <!-- Pricing -->
    @if(Request::is('pricing')) 
      <link href="{{ asset('assets/css/pricing.css') }}" rel="stylesheet">
    @endif

    <!-- Summary -->
    @if(Request::is('summary') || Request::is('register') || Request::is('profile')) 
      <link href="{{asset('assets/css/summary.css')}}" rel="stylesheet" >
      <link href="{{asset('assets/css/custom-select.css')}}" rel="stylesheet" >

      <!-- Intl Dialing Code -->
      <link href="{{ asset('/assets/css/intl-input-custom.css') }}" rel="stylesheet" />
      <link href="{{ asset('/assets/intl-tel-input/css/intlTelInput.min.css') }}" rel="stylesheet" />
      <script type="text/javascript" src="{{ asset('/assets/intl-tel-input/js/intlTelInput.js') }}"></script> 
    @endif

     <!-- Pricing -->
    @if(Request::is('thankyou') || Request::is('thank-confirm')) 
      <link href="{{ asset('assets/css/thankyou.css') }}" rel="stylesheet">
    @endif

</head>
<body>
     <!--Loading Bar-->
    <div class="div-loading">
      <div id="loader" style="display: none;"></div>  
    </div> 

    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
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

                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('pricing') }}">{{ __('Pricing') }}</a>
                            </li>

                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ url('pricing') }}">{{ __('Pricing') }}</a>
                            </li> 

                            <!-- MILESTONE -->
                            @if(Auth::user()->is_admin == 1)
                              <li class="nav-item">
                                  <a class="nav-link" href="{{ url('list-order') }}">Orders</a>
                              </li>
                            @else
                              <li class="nav-item">
                                  <a class="nav-link" href="{{ url('buy-coins') }}">Buy Coins</a>
                              </li> 
                              <li class="nav-item">
                                  <a class="nav-link" href="{{ url('exchange-coins') }}">Exchange Coins</a>
                              </li>
                              <li class="nav-item">
                                  <a class="nav-link" href="{{ url('history-order') }}">Orders and Confirm</a>
                              </li>
                            @endif

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ url('profile') }}">Profile</a>

                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
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
