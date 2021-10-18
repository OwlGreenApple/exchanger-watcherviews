@guest
    @else
    <nav class="sidebar sidebar-offcanvas" id="sidebar">
      <ul class="nav">
        @if (Auth::user()->is_admin == 0)
          <li class="nav-item">
              <a class="nav-link" href="{{ url('buy') }}">
                <i class="mdi mdi-cart-outline"></i> &nbsp;
                {{ Lang::get('transaction.coin.buy') }}
              </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="{{ url('sell') }}">
              <i class="mdi mdi-store-24-hour"></i> &nbsp;
              {{ Lang::get('transaction.sell') }}
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="{{ url('wallet') }}">
              <i class="mdi mdi-wallet-outline"></i> &nbsp;
              Wallet
            </a>
          </li>
        @else
          <li class="nav-item">
            <a class="nav-link" href="{{ url('kurs-admin') }}">
              Kurs Coin
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="{{ url('user-list') }}">
              User List
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="{{ url('order-list') }}">
              Order List
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="{{ url('dispute-admin') }}">
              @if(Price::total_dispute()['new'] == 1) <u>Dispute</u> @else Dispute @endif &nbsp;<span class="badge badge-warning">{{ Price::total_dispute()['total']  }}</span>
            </a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="{{ url('wa-message') }}">
              WA Message
            </a>
          </li>
        @endif
      </ul>
    </nav>
@endguest

<!-- <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-image">
          <img src="../../assets/images/faces/face6.jpg" alt="profile">
          <span class="login-status online"></span>
          
        </div>
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-2">Sara Reley</span>
          <span class="text-secondary text-small">Project Manager</span>
        </div>
        <i class="mdi mdi-bookmark-check text-success nav-profile-badge"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="index.html">
        <span class="menu-title">Home</span>
        <i class="mdi mdi-compass-outline menu-icon"></i>
      </a>
    </li> 
    -->