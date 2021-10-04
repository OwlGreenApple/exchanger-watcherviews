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
      <!-- NAVIGATION -->
      @include('layouts.nav')

      <!-- NAVIGATION RESPONSIVE -->
      @include('layouts.nav-mobile')
        
      <!-- CONTENT -->
      <div class="container-fluid page-body-wrapper justify-content-center">
        <div class="main-panel ">
          <div class="content-wrapper">
            @yield('content')
            <!-- content-wrapper ends -->
            <!-- partial:partials/_footer.html -->

            <!-- FOOTER -->
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
    <!-- end app -->
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
        display_menu_tab();
      });

      function display_menu_tab()
      {
        $(".open_wrapper").click(function(){
            $(".nav-wrapper").toggle();
        });
      }

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