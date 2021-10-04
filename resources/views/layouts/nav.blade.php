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
                   
                  </div>
                  @endif
                </li>

                <li class="nav-item d-none d-lg-block full-screen-link">
                  <a class="nav-link">
                    <i class="mdi mdi-fullscreen" id="fullscreen-button"></i>
                  </a>
                </li>

                <li class="nav-item open_wrapper">
                  <a class="nav-link">
                    <span class="mdi mdi-menu"></span>
                  </a>
                </li>
                <?php } ?>

                <div class="nav-wrapper">
                  @include('layouts.nav-link')
                </div>
          @endguest
     
    </ul>
    
    <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
      <span class="mdi mdi-menu"></span>
    </button>
  </div>
</nav>