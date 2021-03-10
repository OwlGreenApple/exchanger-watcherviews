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

     <!-- Font Awesome 5 -->
    <link href="{{ asset('/assets/font-awesome-5/all.css') }}" rel="stylesheet" />

    <!-- Pricing -->
    @if(Request::is('pricing') || Request::segment(1) == 'checkout') 
      <link href="{{ asset('assets/css/pricing.css') }}" rel="stylesheet">
    @endif

    <!-- Summary -->
    @if(Request::is('summary') || Request::is('register') || Request::is('profile')  || Request::segment(1) == 'referral-reg') 
      <link href="{{asset('assets/css/summary.css')}}" rel="stylesheet" >
      <link href="{{asset('assets/css/custom-select.css')}}" rel="stylesheet" >
   
      <!-- Intl Dialing Code -->
      <link href="{{ asset('/assets/css/intl-input-custom.css') }}" rel="stylesheet" />
      <link href="{{ asset('/assets/intl-tel-input/css/intlTelInput.min.css') }}" rel="stylesheet" />
      <script type="text/javascript" src="{{ asset('/assets/intl-tel-input/js/intlTelInput.js') }}"></script>
    @endif

     <!-- Thankyou -->
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
        <nav class="navbar navbar-expand-md navbar-dark bg-dark shadow-sm">
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
                                <li class="nav-item @if(Request::is('login')) active @endif">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif
                            
                            @if (Route::has('register'))
                                <li class="nav-item @if(Request::is('register')) active @endif">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Daftar') }}</a>
                                </li>
                            @endif

                        @else
                            <!-- MILESTONE -->
                            @if(Auth::user()->is_admin == 1)
                              <li class="nav-item @if(Request::is('list-order')) active @endif">
                                  <a class="nav-link" href="{{ url('list-order') }}">Orders</a>
                              </li>
                              <li class="nav-item @if(Request::is('user-contacts')) active @endif">
                                  <a class="nav-link" href="{{ url('user-contacts') }}">Kontak User</a>
                              </li>
                            @else
                              <li class="nav-item dropdown @if(Request::is('pricing') || Request::segment(2) == 'membership-history') active @endif">
                                  <a id="coinsDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ __('Upgrade') }}
                                  </a>

                                  <div class="dropdown-menu" aria-labelledby="coinsDropdown">
                                    <a class="dropdown-item  @if(Request::is('pricing')) active @endif" href="{{ url('pricing') }}">Upgrade Membership</a>
                                    <a class="dropdown-item  @if(Request::segment(2) == 'membership-history') active @endif" href="{{ url('history-order/membership-history') }}">Upgrade History</a> 
                                  </div>
                              </li> 

                              <li class="nav-item dropdown @if(Request::is('referral')) active @endif">
                                  <a id="coinsDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ __('Referral') }}
                                  </a>

                                  <div class="dropdown-menu" aria-labelledby="coinsDropdown">
                                     <a class="dropdown-item @if(Request::is('referral')) active @endif" href="{{ url('referral') }}">Referral Coins</a> 
                                  </div>
                              </li>

                              <li class="nav-item dropdown @if(Request::is('buy-coins') || Request::segment(2) == 'coin-history') active @endif">
                                  <a id="coinsDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    My coins :  <span><b id="current_coins">{{ number_format(Auth::user()->credits) }}</b></span>
                                  </a>

                                  <div class="dropdown-menu" aria-labelledby="coinsDropdown">
                                     <a class="dropdown-item @if(Request::is('buy-coins')) active @endif" href="{{ url('buy-coins') }}">Buy Coins</a> 
                                     <a class="dropdown-item  @if(Request::is('coin-history') && Request::segment(2) == 'coin-history' ) active @endif" href="{{ url('history-order/coin-history') }}">Coin Order History</a> 
                                  </div>
                              </li>
                               
                              <li class="nav-item dropdown @if(Request::is('exchange-coins') || Request::is('transaction')) active @endif">
                                  <a id="coinsDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Views
                                  </a>

                                  <div class="dropdown-menu" aria-labelledby="coinsDropdown">
                                    <a class="dropdown-item  @if(Request::is('exchange-coins')) active @endif" href="{{ url('exchange-coins') }}">Buy Views</a>
                                    <a class="dropdown-item  @if(Request::is('transaction')) active @endif" href="{{ url('transaction') }}">View History</a>
                                  </div>
                              </li>
                            @endif

                            <li class="nav-item dropdown @if(Request::is('profile') || Request::is('contact')) active @endif">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item">Membership
                                     : @if(Auth::user()->membership == null || Auth::user()->membership =="") Free @else <span class="badge badge-success px-2 py-1">{{ Auth::user()->membership }}</span> @endif</a>
                                    <a class="dropdown-item @if(Request::is('profile')) active @endif" href="{{ url('profile') }}">Profil</a>
                                    @if(Auth::user()->is_admin == 0)
                                      <a class="dropdown-item @if(Request::is('contact')) active @endif" href="{{ url('contact') }}">Kontak Kami</a>
                                    @endif

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

        <main>
            @yield('content')
        </main>
    </div>

    <script type="text/javascript">
      //wrapper alert style
      function alert_wrapper(text)
      {
        return  "<div class='alert alert-danger'>"+text+"</div>";
      }
    </script>

</body>
</html>
