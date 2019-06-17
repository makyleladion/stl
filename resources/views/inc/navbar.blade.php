        <!-- Navbar (Header) Starts-->
        <nav class="navbar navbar-expand-lg navbar-light bg-faded">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" data-toggle="collapse" class="navbar-toggle d-lg-none float-left">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <form role="search" class="navbar-form navbar-right mt-1">
                        <div class="position-relative has-icon-right">
                            <input type="text" placeholder="Search" class="form-control round" />
                            <div class="form-control-position"><i class="ft-search"></i></div>
                        </div>
                    </form>
                </div>
                <div class="navbar-container">
                    <div id="navbarSupportedContent" class="collapse navbar-collapse">
                        <ul class="navbar-nav">
                            <li class="nav-item mr-2">
                                <a id="navbar-fullscreen" href="javascript:;" class="nav-link apptogglefullscreen">
                                    <i class="ft-maximize font-medium-3 blue-grey darken-4"></i>
                                    <p class="d-none">fullscreen</p>
                                </a>
                            </li>
                            <li class="dropdown nav-item">
                                <a id="dropdownBasic3" href="#" data-toggle="dropdown" class="nav-link position-relative dropdown-toggle">
                                    <i class="ft-user font-medium-3 blue-grey darken-4"></i>
                                    <p class="d-none">User Settings</p>
                                </a>
                                <div ngbdropdownmenu="" aria-labelledby="dropdownBasic3" class="dropdown-menu dropdown-menu-right">
                                    <a href="#" class="dropdown-item py-1">
                                        <i class="ft-settings mr-2"></i>
                                        <span>Account Settings</span>
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a {{ route('user-logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="dropdown-item">
                                        <i class="ft-power mr-2"></i>
                                        <span>Logout</span>
                                    </a>
                                    <form id="logout-form" action="{{ route('user-logout') }}" method="POST" style="display: none;"> {{ csrf_field() }} </form>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a href="javascript:;" class="nav-link position-relative notification-sidebar-toggle">
                                    <i class="ft-align-left font-medium-3 blue-grey darken-4"></i>
                                    <p class="d-none">Notifications Sidebar</p>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>
        <!-- Navbar (Header) Ends-->