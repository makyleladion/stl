        <!-- main menu-->
        <!--.main-menu(class="#{menuColor} #{menuOpenType}", class=(menuShadow == true ? 'menu-shadow' : ''))-->
        <div data-active-color="white" data-background-color="white" data-image="" class="app-sidebar">
            <!-- main menu header-->
            <!-- Sidebar Header starts-->
            <div class="sidebar-header">
                <div class="logo clearfix">
                    <a href="#" class="logo-text float-left">
                        <div class="logo-img">
                            <img src="{{url('/assets/img/3a8-logo.png')}}"/>
                        </div>
                        <span class="text align-middle">3a8 Gaming</span>
                    </a>
                    <a id="sidebarToggle" href="javascript:;" class="nav-toggle d-none d-sm-none d-md-none d-lg-block">
                        <i data-toggle="expanded" class="ft-toggle-right toggle-icon"></i>
                    </a>
                    <a id="sidebarClose" href="javascript:;" class="nav-close d-block d-md-block d-lg-none d-xl-none"><i class="ft-x"></i></a>
                </div>
            </div>
            <!-- Sidebar Header Ends-->
            <!-- / main menu header-->
            <!-- main menu content-->
            <div class="sidebar-content">
                <div class="nav-container">
                    <ul id="main-menu-navigation" data-menu="menu-navigation" class="navigation navigation-main">
                        <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}"><a href="{{ route('dashboard')}}" data-page-url="/{{ route('dashboard')}}" ><i class="ft-home"></i><span data-i18n="" class="menu-title">Dashboard</span></a></li>
                        <li class="has-sub nav-item {{ request()->is('transactions/all') ? 'show' : '' }}{{ request()->is('transactions/all-canceled') ? 'show' : '' }}"><a href="#"><i class="icon-docs"></i><span data-i18n="" class="menu-title">Transactions</span></a>
                            <ul class="menu-content">
                                <li class="{{ request()->is('transactions/all') ? 'active' : '' }}"><a href="{{ route('all-transactions')}}" data-page-url="/{{ route('all-transactions')}}" class="menu-item">All</a>
                                <li class="{{ request()->is('transactions/all-canceled') ? 'active' : '' }}"><a href="{{ route('all-transactions-canceled')}}" data-page-url="/{{ route('all-transactions-canceled')}}" class="menu-item">Cancelled</a>
                            </ul>
                        </li>
                        <li class="nav-item {{ request()->is('payouts/all') ? 'active' : '' }}"><a href="{{ route('all-payouts')}}" data-page-url="/{{ route('all-payouts')}}"><i class="ft-user-check"></i><span data-i18n="" class="menu-title">Payouts</span></a></li>
                        <li class="nav-item {{ request()->is('outlets/all') ? 'active' : '' }} {{ request()->is('outlets/create') ? 'active' : '' }} {{ request()->is('outlets/edit/*') ? 'active' : '' }}"><a href="{{ route('all-outlets')}}" data-page-url="/{{ route('all-outlets')}}"><i class="icon-home"></i><span data-i18n="" class="menu-title">Outlets</span></a></li>                      
                        <li class="nav-item {{ request()->is('users/all') ? 'active' : '' }} {{ request()->is('users/create') ? 'active' : '' }} {{ request()->is('users/edit/*') ? 'active' : '' }} {{ request()->is('users/view-log/*') ? 'active' : '' }}"><a href="{{ route('all-users')}}" data-page-url="/{{ route('all-users')}}"><i class="icon-users"></i><span data-i18n="" class="menu-title">Users</span></a></li>
                        <li class="nav-item {{ request()->is('settings') ? 'active' : '' }}"><a href="{{ route('settings')}}" data-page-url="/{{ route('reports-highest-bet')}}"><i class="icon-settings"></i></i><span data-i18n="" class="menu-title">Settings</span></a></li>
                        <li class="has-sub nav-item {{ request()->is('reports/summary') ? 'show' : '' }}{{ request()->is('reports/highest-bet') ? 'show' : '' }}{{ request()->is('reports/hotnumbers') ? 'show' : '' }}"><a href="#"><i class="icon-doc"></i><span data-i18n="" class="menu-title">Reports</span></a>
                            <ul class="menu-content">
                                <li class="{{ request()->is('reports/summary') ? 'active' : '' }}"><a href="{{ route('reports-summary')}}" class="menu-item" data-page-url="/{{ route('reports-summary')}}">Summary Reports</a>
                                <li class="{{ request()->is('reports/highest-bet') ? 'active' : '' }}"><a href="{{ route('reports-highest-bet') }}" class="menu-item" data-page-url="/{{ route('reports-highest-bet')}}">Highest Bet</a>
                                <li class="{{ request()->is('reports/hotnumbers') ? 'active' : '' }}"><a href="{{ route('reports-hotnumbers') }}" class="menu-item" data-page-url="/{{ route('reports-hotnumbers')}}">Hot Numbers</a>
                            </ul>
                        </li>                      
                    </ul>
                </div>
            </div>
            <!-- main menu content-->
            <div class="sidebar-background"></div>
            <!-- main menu footer-->
            <!-- include includes/menu-footer-->
            <!-- main menu footer-->
        </div>
        <!-- / main menu-->