@if (Auth::user()->is_admin == 0)
  <li class="nav-item nav-logout d-none d-lg-block">
    <a class="nav-link">
      <i class="fas fa-coins"></i>&nbsp;{{ Lang::get('custom.currency') }} {{ Price::get_rate() }}/coin
    </a>
  </li> 
  <li class="nav-item nav-logout d-none d-lg-block">
    <a class="nav-link" href="{{ url('buy') }}">
      <i class="mdi mdi-cart-outline"></i> &nbsp;
      {{ Lang::get('transaction.coin.buy') }}
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
    <a class="nav-link" href="{{ url('atm-coupon-list') }}">
      Kupon
    </a>
  </li>
  <li class="nav-item nav-logout d-none d-lg-block">
    <a class="nav-link" href="{{ url('user-to-user') }}">
       Transaksi
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
@endif

<!--
    https://materialdesignicons.com/
     <div class="dropdown-divider"></div>
        
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
        <h6 class="p-3 mb-0 text-center">See all notifications</h6> 
    -->

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

