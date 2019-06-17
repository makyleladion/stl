<aside id="aside" class="aside aside-left" data-fuse-bar="aside" data-fuse-bar-media-step="md" data-fuse-bar-position="left">
  <div class="aside-content-wrapper">

    <div class="aside-content bg-primary-500 text-auto">

      <div class="aside-toolbar">



        <div class="logo">
          <a href="{{url('/')}}" class="site-logo"><img src="{{url('/assets/images/favicon-32x32.png')}}" class="d-inline-block align-left site-logo" alt=""><span class="logo-text">Small Town Lottery</span></a>
        </div>

        <button id="toggle-fold-aside-button" type="button" class="btn btn-icon d-none d-lg-block" data-fuse-aside-toggle-fold>
          <i class="icon icon-backburger"></i>
        </button>

      </div>

      <ul class="nav flex-column custom-scrollbar" id="sidenav" data-children=".nav-item">

        <li class="subheader">
          <span>MAIN</span>
        </li>

        <li class="nav-item">
          <a class="nav-link ripple {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard')}}" data-page-url="/{{ route('dashboard')}}">
            <i class="icon s-4 icon-tile-four"></i>
            <span>Dashboard</span>
          </a>
        </li>

        <li class="nav-item" role="tab" id="heading-outlets">

          <a class="nav-link ripple with-arrow" data-toggle="collapse" data-target="#collapse-transaction" href="#" aria-expanded="true" aria-controls="collapse-transaction">
            <i class="icon s-4 icon-home-outline"></i>
            <span>Transactions</span>
          </a>

          <ul id="collapse-transaction" class="collapse {{ request()->is('transactions/all') ? 'show' : '' }}{{ request()->is('transactions/all-canceled') ? 'show' : '' }}" role="tabpanel" aria-labelledby="heading-transactions" data-children=".nav-item">

            <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('transactions/all') ? 'active' : '' }}" href="{{ route('all-transactions')}}" data-page-url="/{{ route('all-transactions')}}">

                <span>All</span>
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('transactions/all-canceled') ? 'active' : '' }}" href="{{ route('all-transactions-canceled')}}" data-page-url="/{{ route('all-transactions-canceled')}}">

                <span>Canceled</span>
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('transactions/invalid-tickets') ? 'active' : '' }}" href="{{ route('transactions-invalid-tickets')}}" data-page-url="/{{ route('transactions-invalid-tickets')}}">

                <span>Invalidated</span>
              </a>
            </li>

          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link ripple {{ request()->is('payouts/all') ? 'active' : '' }}" href="{{ route('all-payouts')}}" data-page-url="/{{ route('all-payouts')}}">
            <i class="icon s-4 icon-account"></i>
            <span>Payouts</span>
          </a>
        </li>

        <li class="nav-item" role="tab" id="heading-outlets">

          <a class="nav-link ripple with-arrow" data-toggle="collapse" data-target="#collapse-outlets" href="#" aria-expanded="true" aria-controls="collapse-outlets">
            <i class="icon s-4 icon-home-outline"></i>
            <span>Outlets</span>
          </a>

          <ul id="collapse-outlets" class="collapse {{ request()->is('outlets/all') ? 'show' : '' }}{{ request()->is('outlets/create') ? 'show' : '' }}" role="tabpanel" aria-labelledby="heading-outlets" data-children=".nav-item">

            <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('outlets/all') ? 'active' : '' }}" href="{{ route('all-outlets')}}" data-page-url="/{{ route('all-outlets')}}">

                <span>All</span>
              </a>
            </li>

			@if (!auth()->user()->is_read_only)
            <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('outlets/create') ? 'active' : '' }}" href="{{ route('new-outlet')}}" data-page-url="/{{ route('new-outlet')}}">

                <span>New</span>
              </a>
            </li>
            @endif

          </ul>
        </li>

        <li class="nav-item" role="tab" id="heading-memos">

          <a class="nav-link ripple with-arrow" data-toggle="collapse" data-target="#collapse-memos" href="#" aria-expanded="true" aria-controls="collapse-memos">
            <i class="icon s-4 icon-message-text"></i>
            <span>Memos</span>
          </a>

          <ul id="collapse-memos" class="collapse {{ request()->is('memos/all') ? 'show' : '' }}{{ request()->is('memos/create') ? 'show' : '' }}" role="tabpanel" aria-labelledby="heading-memos" data-children=".nav-item">

            <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('memos/all') ? 'active' : '' }}" href="{{ route('all-memos')}}" data-page-url="/{{ route('all-memos')}}">

                <span>All</span>
              </a>
            </li>

            <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('memos/create') ? 'active' : '' }}" href="{{ route('new-memo')}}" data-page-url="/{{ route('new-memo')}}">

                <span>New</span>
              </a>
            </li>

          </ul>
        </li>

        <li class="nav-item" role="tab" id="heading-users">

          <a class="nav-link ripple with-arrow" data-toggle="collapse" data-target="#collapse-users" href="#" aria-expanded="true" aria-controls="collapse-users">
            <i class="icon s-4 icon-account-multiple"></i>
            <span>Users</span>
          </a>

          <ul id="collapse-users" class="collapse {{ request()->is('users/all') ? 'show' : '' }}{{ request()->is('users/new') ? 'show' : '' }}" role="tabpanel" aria-labelledby="heading-users" data-children=".nav-item">

            <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('users/all') ? 'active' : '' }}" href="{{ route('all-users')}}" data-page-url="/{{ route('all-users')}}">

                <span>All</span>
              </a>
            </li>

			@if (!auth()->user()->is_read_only)
            <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('users/create') ? 'active' : '' }}" href="{{ route('new-user')}}" data-page-url="/{{ route('new-user')}}">

                <span>New</span>
              </a>
            </li>
            @endif

          </ul>
        </li>

        <li class="nav-item" role="tab" id="heading-settings">

          <a class="nav-link ripple with-arrow" data-toggle="collapse" data-target="#collapse-settings" href="#" aria-expanded="true" aria-controls="collapse-settings">
            <i class="icon s-4 icon-settings"></i>
            <span>Settings</span>
          </a>

          <ul id="collapse-settings" class="collapse {{ request()->is('settings/bet-reactivation') ? 'show' : '' }}{{ request()->is('settings/sms-notification') ? 'show' : '' }}" role="tabpanel" aria-labelledby="heading-settings" data-children=".nav-item">

            <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('settings/bet-reactivation') ? 'active' : '' }}" href="{{ route('bet-reactivation')}}" data-page-url="/{{ route('bet-reactivation')}}">
                <span>Bet Reactivation</span>
              </a>
            </li>

             <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('settings/sms-notification') ? 'active' : '' }}" href="{{ route('sms-notification')}}" data-page-url="/{{ route('sms-notification')}}">
                <span>SMS Notification</span>
              </a>
            </li>


          </ul>
        </li>

        <li class="nav-item"  role="tab" id="heading-reports">

          <a class="nav-link ripple with-arrow" data-toggle="collapse" data-target="#collapse-reports" href="#" aria-expanded="true" aria-controls="collapse-reports">
            <i class="icon s-4 icon-clipboard-text"></i>
            <span>Reports</span>
          </a>

          <ul id="collapse-reports" class="collapse {{ request()->is('reports/summary') ? 'show' : '' }}{{ request()->is('reports/highest-bet') ? 'show' : '' }}{{ request()->is('reports/hotnumbers') ? 'show' : '' }}{{ request()->is('reports/summary-range') ? 'show' : '' }}{{ request()->is('reports/summary-range/winnings') ? 'show' : '' }}" role="tabpanel" aria-labelledby="heading-reports" data-children=".nav-item">

            <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('reports/summary') ? 'active' : '' }}" href="{{ route('reports-summary')}}" data-page-url="/{{ route('reports-summary')}}">
                <span>Summary Reports</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('reports/highest-bet') ? 'active' : '' }}" href="{{ route('reports-highest-bet') }}" data-page-url="/{{ route('reports-highest-bet')}}">
                <span>Highest Bet</span>
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link ripple {{ request()->is('reports/hotnumbers') ? 'active' : '' }}" href="{{ route('reports-hotnumbers') }}" data-page-url="/{{ route('reports-hotnumbers')}}">
                <span>Hot Numbers</span>
              </a>
            </li>
            <li class="nav-item" role="tab" id="heading-reports-calculations">
            
            	<a class="nav-link ripple with-arrow" data-toggle="collapse" data-target="#collapse-reports-calculations" href="#" aria-expanded="true" aria-controls="collapse-reports-calculations">
                <span>Date Range Calculation</span>
              </a>
              
              <ul id="collapse-reports-calculations" class="collapse {{ request()->is('reports/summary-range') ? 'show' : '' }}{{ request()->is('reports/summary-range/winnings') ? 'show' : '' }}" role="tabpanel" aria-labelledby="heading-reports-calculations" data-children=".nav-item">
              	<li class="nav-item">
              		<a class="nav-link ripple {{ request()->is('reports/summary-range') ? 'active' : '' }}" href="{{ route('reports-date-range-calculations')}}" data-page-url="/{{ route('reports-date-range-calculations')}}">
                    <span>Sales</span>
                  </a>
              	</li>
              	<li class="nav-item">
              		<a class="nav-link ripple {{ request()->is('reports/summary-range/winnings') ? 'active' : '' }}" href="{{ route('reports-date-range-calculations-winnings')}}" data-page-url="/{{ route('reports-date-range-calculations-winnings')}}">
                    <span>Winnings (Payouts)</span>
                  </a>
              	</li>
              </ul>
            </li>

          </ul>
        </li>



      </ul>
    </div>
  </div>
</aside>
