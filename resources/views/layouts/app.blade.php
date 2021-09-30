<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <!-- Required meta tags -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Exchanger</title>
    <!-- Styles -->
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/main.css') }}" rel="stylesheet">
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ asset('assets/template/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/template/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assets/template/vendors/font-awesome/css/font-awesome.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('assets/template/vendors/bootstrap-datepicker/bootstrap-datepicker.min.css')}}">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="{{ asset('assets/template/css/demo_1/style.css') }}">
    <!-- End layout styles -->
    <link rel="shortcut icon" href="{{url('assets/img/favicon.png')}}"/>
    <script src="{{ asset('assets/js/jquery-3.2.1.min.js') }}"></script>


    <!-- Scripts 
    <script src="{{ asset('assets/js/app.js') }}" defer></script>
    <script src="{{ asset('assets/js/jquery-3.2.1.min.js') }}"></script>-->

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

    <!-- Cropper -->
    <script type="text/javascript" src="{{ asset('assets/cropper/cropper.min.js') }}"></script>
    <link href="{{ asset('assets/cropper/cropper.min.css') }}" rel="stylesheet" />

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
      <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
          <a class="navbar-brand brand-logo" href="{{ url('home')}}"><img src="{{url('assets/img/logo.png')}}" alt="logo" /></a>
          
          <a class="navbar-brand brand-logo-mini" href="{{ url('home')}}"><img src="{{url('assets/img/favicon.png')}}" alt="logo" /></a>
          
        </div>
        <div class="navbar-menu-wrapper d-flex align-items-stretch">
        <!--
          <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
            <span class="mdi mdi-menu"></span>
          </button>
          <div class="search-field d-none d-md-block">
            <form class="d-flex align-items-center h-100" action="#">
              <div class="input-group">
                <div class="input-group-prepend bg-transparent">
                  <i class="input-group-text border-0 mdi mdi-magnify"></i>

                </div>
                <input type="text" class="form-control bg-transparent border-0" placeholder="Search projects">
              </div>
            </form>
          </div>
          -->
          <ul class="navbar-nav navbar-nav-right">
          @guest
            @else
            <?php if (Auth::check()) {?>

            <li class="nav-item nav-profile dropdown">
              <a class="nav-link dropdown-toggle" id="profileDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                <!--<div class="nav-profile-img">
                  <img src="../assets/images/faces/face1.jpg" alt="image">
                  <span class="availability-status online"></span>
                </div>-->
                <div class="nav-profile-text">
                    @if(Auth::check() == true)
                        <p class="mb-1 text-black">{{ ucfirst(Auth::user()->name) }}&nbsp;({{ Auth::user()->membership }})</p>
                    @endif
                </div>
              </a>
              <div class="dropdown-menu navbar-dropdown" aria-labelledby="profileDropdown">
                <a class="dropdown-item" href="{{ url('account') }}">
                  <i class="mdi mdi-cached mr-2 text-success"></i> Account </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ url('logout') }}" onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                  <i class="mdi mdi-logout mr-2 text-primary"></i> Log out </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>

              </div>
            </li>
            <li class="nav-item d-none d-lg-block full-screen-link">
              <a class="nav-link">
                <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
              </a>
            </li>
            <!--
            <li class="nav-item dropdown">
              <a class="nav-link count-indicator dropdown-toggle" id="messageDropdown" href="#" data-toggle="dropdown" aria-expanded="false">
                <i class="mdi mdi-email-outline"></i>
                <span class="count-symbol bg-warning"></span>
              </a>
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="messageDropdown">
                <h6 class="p-3 mb-0">Messages</h6>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="../assets/images/faces/face4.jpg" alt="image" class="profile-pic">
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Mark send you a message</h6>
                    <p class="text-gray mb-0"> 1 Minutes ago </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="../assets/images/faces/face2.jpg" alt="image" class="profile-pic">
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Cregh send you a message</h6>
                    <p class="text-gray mb-0"> 15 Minutes ago </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <img src="../assets/images/faces/face3.jpg" alt="image" class="profile-pic">
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject ellipsis mb-1 font-weight-normal">Profile picture updated</h6>
                    <p class="text-gray mb-0"> 18 Minutes ago </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <h6 class="p-3 mb-0 text-center">4 new messages</h6>
              </div>
            </li>
            -->
            <li class="nav-item dropdown">
              <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
                <i class="mdi mdi-bell-outline"></i>
                @if(Price::events()['total'] > 0)
                  <span class="count-symbol bg-danger"></span>
                @endif
              </a>
              @if(Price::events()['total'] > 0)
              <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" aria-labelledby="notificationDropdown">
                <h6 class="p-3 mb-0">Notifikasi</h6>
                <div class="dropdown-divider"></div>

                @foreach(Price::events()['data'] AS $row)
                  @if($row->type == 0)
                    <a target="_blank" data-id="{{ $row->id }}" class="dropdown-item preview-item evt">
                      <div class="preview-thumbnail">
                        <div class="preview-icon bg-warning">
                          <i class="mdi mdi-settings"></i>
                        </div>
                      </div>
                      <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                        <h6 class="preview-subject font-weight-normal mb-1">{{ $row->event_name }}</h6>
                        <p style="max-height: 55px" class="text-gray ellipsis mb-0">{!! $row->message !!}</p>
                      </div>
                    </a>
                    <div class="dropdown-divider"></div>
                  @else
                    <a target="_blank" data-id="{{ $row->id }}" class="dropdown-item preview-item evt">
                      <div class="preview-thumbnail">
                        <div class="preview-icon bg-success">
                          <i class="mdi mdi-calendar"></i>
                        </div>
                      </div>

                      <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                        <h6 class="preview-subject font-weight-normal mb-1">{{ $row->event_name }}</h6>
                        <p style="max-height: 55px" class="text-gray ellipsis mb-0">{!! $row->message !!}</p>
                      </div>
                    </a>
                    <div class="dropdown-divider"></div>
                  @endif
                @endforeach
                <!-- <div class="dropdown-divider"></div>
                
                <a class="dropdown-item preview-item">
                  <div class="preview-thumbnail">
                    <div class="preview-icon bg-info">
                      <i class="mdi mdi-link-variant"></i>
                    </div>
                  </div>
                  <div class="preview-item-content d-flex align-items-start flex-column justify-content-center">
                    <h6 class="preview-subject font-weight-normal mb-1">Launch Admin</h6>
                    <p class="text-gray ellipsis mb-0"> New admin wow! </p>
                  </div>
                </a>
                <div class="dropdown-divider"></div>
                <h6 class="p-3 mb-0 text-center">See all notifications</h6> -->
              </div>
              @endif
            </li>
            <?php } ?>
            <!--
            https://materialdesignicons.com/
            -->
            
              @if (Auth::user()->is_admin == 0)
                <li class="nav-item nav-logout d-none d-lg-block">
                  <a class="nav-link">
                    <i class="fas fa-coins"></i>&nbsp;{{ Lang::get('custom.currency') }} {{ Price::get_rate() }}/coin
                  </a>
                </li> 
                <li class="nav-item nav-logout d-none d-lg-block">
                  <a class="nav-link" href="{{ url('buy') }}">
                    <i class="mdi mdi-cart-outline"></i> &nbsp;
                    Beli Koin
                  </a>
                </li>
                <li class="nav-item nav-logout d-none d-lg-block">
                  <a class="nav-link" href="{{ url('sell') }}">
                    <i class="mdi mdi-store-24-hour"></i> &nbsp;
                    {{ Lang::get('transaction.sell') }}
                  </a>
                </li>
                <li class="nav-item nav-logout d-none d-lg-block">
                  <a class="nav-link" href="{{ url('wallet') }}">
                    <i class="mdi mdi-wallet-outline"></i> &nbsp;
                    Wallet
                  </a>
                </li>
              @else
                <li class="nav-item nav-logout d-none d-lg-block">
                  <a class="nav-link" href="{{ url('kurs-admin') }}">
                    Kurs Coin
                  </a>
                </li>
                <li class="nav-item nav-logout d-none d-lg-block">
                  <a class="nav-link" href="{{ url('user-list') }}">
                    User List
                  </a>
                </li>
                <li class="nav-item nav-logout d-none d-lg-block">
                  <a class="nav-link" href="{{ url('order-list') }}">
                    Order List
                  </a>
                </li>
                <li class="nav-item nav-logout d-none d-lg-block">
                  <a class="nav-link" href="{{ url('dispute-admin') }}">
                    @if(Price::total_dispute()['new'] == 1) <u>Dispute</u> @else Dispute @endif &nbsp;<span class="badge badge-warning">{{ Price::total_dispute()['total']  }}</span>
                  </a>
                </li>
                <li class="nav-item nav-logout d-none d-lg-block">
                  <a class="nav-link" href="{{ url('wa-message') }}">
                    WA Message
                  </a>
                </li>
              @endif
            @endguest
          </ul>
          
          <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
            <span class="mdi mdi-menu"></span>
          </button>
        </div>
      </nav>
        <!--
        <main class="py-4 ">
        -->
      <div class="container-fluid page-body-wrapper justify-content-center">
        <div class="main-panel ">
          <div class="content-wrapper">
            @yield('content')
            <!-- content-wrapper ends -->
            <!-- partial:partials/_footer.html -->
            <footer class="footer">
              <div class="d-sm-flex justify-content-center justify-content-sm-between">
                <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">Copyright © 2021 <a href="https://exchangerwatcherviews.com/" target="_blank">Exchangerwatcherviews</a>. All rights reserved.</span>
                <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">Bangga menggunakan produk lokal <i class="mdi mdi-heart text-danger"></i></span>
              </div>
            </footer>        
          </div>
          
        </div>
      </div>
        <!--
        </main>
        -->
    </div>
    <script src="{{ asset('assets/template/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('assets/template/vendors/chart.js/Chart.min.js')}}"></script>
    <script src="{{ asset('assets/template/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('assets/template/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/template/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/template/js/misc.js') }}"></script>
    <script src="{{ asset('assets/template/js/settings.js') }}"></script>
    <script src="{{ asset('assets/template/js/todolist.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page -->
    <script src="{{ asset('assets/template/js/dashboard.js') }}"></script>
    <!-- End custom js for this page -->

    <!-- event -->
    <script type="text/javascript">
      $(document).ready(function(){
        change_event_notification();
      });

      function change_event_notification()
      {
        $(".evt").click(function(){
            var id = $(this).attr('data-id');

            $.ajax({
                type : 'GET',
                url : "{{ url('change-event') }}",
                dataType : 'json',
                data : {'id':id},
                beforeSend: function()
                {
                   $('#loader').show();
                   $('.div-loading').addClass('background-load');
                },
                success : function(result)
                {
                    if(result.res == 1)
                    {
                      $('#loader').hide();
                      $('.div-loading').removeClass('background-load');
                      $("#err_message").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>');
                    }
                    else
                    {
                      location.href="{{ url('/') }}/"+result.res;
                    }
                },
                error : function()
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');
                }
            });
        });
      }
    </script>
  </body>
</html>