@guest @else

<nav id="toolbar" class="fixed-top bg-white">

    <div class="row no-gutters align-items-center flex-nowrap">

        <div class="col">

            <div class="row no-gutters align-items-center flex-nowrap">

                <button type="button" class="toggle-aside-button btn btn-icon d-block d-lg-none" data-fuse-bar-toggle="aside"> <i class="icon icon-menu"></i> </button>

                <div class="toolbar-separator d-block d-lg-none"></div>

                @if (!auth()->user()->is_admin)
                <div class="shortcuts-wrapper row no-gutters align-items-center px-0 px-sm-2">

                    <div class="shortcuts row no-gutters align-items-center d-none d-md-flex">
                        <a class="navbar-brand" href="{{url('/')}}"> <img src="{{url('/assets/images/favicon-32x32.png')}}" width="32" height="32" class="d-inline-block align-middle mr-3 ml-2" alt=""> Small Town Lottery </a>
                    </div>

                </div>

                <div class="toolbar-separator"></div>                    

                <div class="shortcuts-wrapper row no-gutters align-items-center px-0 px-sm-2">

                    <div class="shortcuts row no-gutters align-items-center d-none d-md-flex">
                        <!--CLOCK DISPLAY-->
                        <span class="clock"></span>
                        <script type="text/javascript">
                            jQuery(function($) {
                                setInterval(function() {
                                    var date = new Date(),
                                        time = date.toLocaleTimeString();
                                    $(".clock").html(time);
                                }, 1000);
                            });
                        </script>
                    </div>

                </div>

                <div class="toolbar-separator"></div>

                @endif

                <div class="shortcuts-wrapper row no-gutters align-items-center px-0 px-sm-2">

                    <div id="previous_results" class="shortcuts row no-gutters align-items-center d-none d-md-flex">

                        <div class="col mx-4">

                            @if (\Route::current()->getName() == 'dashboard' || \Route::current()->getName() == 'outlet-dashboard')
                            <input type="text" id="time-machine-datepicker" class="h6 custom-select form-control" placeholder="Pick a Date">
                            <script>
                            $(document).ready(function() {
                                $('#time-machine-datepicker').datepicker({format:'yyyy-mm-dd'});
                                $('#time-machine-datepicker').change(function() {
                                	@if (\Route::current()->getName() == 'dashboard')
																	var url = "{{ route(\Route::current()->getName()) }}/" + $(this).val();
																	@elseif (\Route::current()->getName() == 'outlet-dashboard')
																	var url = "{{ route(\Route::current()->getName(), ['outlet_id' => $outlet_id]) }}/" + $(this).val();
																	@endif
																	window.location.href = url;
                                });
                            });
                            </script>
                            @endif

                        </div>

                    </div>
                </div>

                <div class="shortcuts-wrapper row no-gutters align-items-center px-0 px-sm-2" style="display:none">

                    <div class="shortcuts row no-gutters align-items-center d-none d-md-flex">

                        <a href="#" class="shortcut-button btn btn-icon mx-1" title="Dashboard">
                            <i class="icon icon-star text-amber-600"></i>
                        </a>

                        <a href="#" class="shortcut-button btn btn-icon mx-1" title="Transactions">
                            <i class="icon icon-folder"></i>
                        </a>

                        <a href="#" class="shortcut-button btn btn-icon mx-1" title="Payouts">
                            <i class="icon icon-account-plus"></i>
                        </a>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-auto">

            <div class="row no-gutters align-items-center justify-content-end">

                <div class="shortcuts-wrapper row no-gutters align-items-center px-0 px-sm-2">

                    <div class="shortcuts row no-gutters align-items-center d-none d-md-flex">

                        <span class="shortcut-button btn btn-icon mx-1">

                          <?php
                            if ( isset($outlet_name) ) {
                              echo $outlet_name;
                            }
                          ?>

                        </span>

                    </div>

                </div>


                @if(!auth()->user()->is_admin)
                <div class="toolbar-separator"></div>

                <div class="notification-menu-button dropdown">
                  <div class="ripple row align-items-center no-gutters px-2 px-sm-4" role="button" id="dropdownNotificationsMenu" data-toggle="dropdown" style="overflow:unset;">
                      <i class="status icon-earth"></i>@if(count(auth()->user()->unreadNotifications)>0)<span id="memoCounter" class="badge" style="color:white;background:red;margin-top:-10px;margin-left:14px;display: block; position: absolute;">{{ count(auth()->user()->unreadNotifications) }}</span>@endif
                  </div>
                  <div class="dropdown-menu dropdown-menu-left" aria-labelledby="dropdownUserMenu">
                    <?php $a = 1; $nicon = ""?>
                    @foreach(auth()->user()->notifications as $notification)
                      <?php
                        $nicon = "icon-checkbox-blank-circle-outline";
                        if ($notification->unread()) {
                          $nicon = "icon-checkbox-blank-circle";

                        }
                      ?>
                    <a class="dropdown-item" href="#" style="padding-top: .6rem;line-height: 1.3rem;height: 3.8rem;">
                        <div class="row no-gutters align-items-center flex-nowrap">
                          <i class="{{ $nicon }}" id="readIcon-{{$notification->data['memo']['id']}}" style="font-size: 1rem; width: 1rem;"></i>
                          <div class="px-3" role="button" data-toggle="modal" data-target="#MemoForm" data-memoid="{{$notification->data['memo']['id']}}" data-notifid="{{ $notification->id }}"  aria-pressed="true">
                            {{$notification->data['announcer']['name']}} made an announcement
                            <div  style="font-size:1rem;">
                              {{  Carbon\Carbon::parse($notification->data['memo']['datetime']['date'])->toDayDateTimeString() }}
                            </div>
                          </div>
                        </div>
                    </a>
                      <?php $a++; ?>
                      @if($a<=count((auth()->user()->notifications)))
                        <div class="dropdown-divider"></div>
                      @endif
                    @endforeach
                  </div>
                </div>
                @endif

                <div class="toolbar-separator"></div>
                <div class="user-menu-button dropdown">

                    <div class="dropdown-toggle ripple row align-items-center no-gutters px-2 px-sm-4" role="button" id="dropdownUserMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">

                        <div class="avatar-wrapper"> <img class="avatar" src="{{url('assets/images/avatars/profile.jpg')}}"> <i class="status text-green icon-checkbox-marked-circle s-4"></i> </div> <span class="username mx-3 d-none d-md-block">{{ Auth::user()->name }}</span>

                    </div>

                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownUserMenu">
                        @if (!auth()->user()->is_admin)
                        <a class="dropdown-item" href="#">
                            <div class="row no-gutters align-items-center flex-nowrap"> 
                                <i class="status icon-ticket-star"></i> 
                                <span class="px-3" role="button" data-toggle="modal" data-target="#NewTicketForm" aria-pressed="true">Bet Ticket</span>
                            </div>
                        </a>

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item" href="#">
                            <div class="row no-gutters align-items-center flex-nowrap"> 
                                <i class="status icon-account-check"></i> 
                                <span class="px-3" role="button" data-toggle="modal" data-target="#SearchWinResult" aria-pressed="true">Payout</span>
                            </div>
                        </a>

                        <a class="dropdown-item" href="#">
                            <div class="row no-gutters align-items-center flex-nowrap"> 
                                <i class="status icon-printer"></i> 
                                <span class="px-3 end-of-the-day-sales-popup" role="button" data-toggle="modal" data-target="#EndOfTheDaySales" aria-pressed="true">Daily Sales</span>
                            </div>
                        </a>

                        <div class="dropdown-divider"></div>

												@if(env('IS_OFFLINE',false))
                        <a class="dropdown-item" href="#">
                            <div class="row no-gutters align-items-center flex-nowrap"> <i class="status icon-cloud-upload"></i> <span class="px-3" role="button" data-toggle="modal" data-target="" aria-pressed="true" onClick="window.open('{{ route('export-unsync-transactions') }}')">Export Transactions <span class="badge" style="color:red">{{ isset($count_unsync) ? $count_unsync : 0 }}</span></span>
                            </div>
                        </a>
                        
                        <div class="dropdown-divider"></div>                        
                        
                        <a class="dropdown-item" href="#">
                            <div class="row no-gutters align-items-center flex-nowrap"> <i class="status icon-transfer"></i> <span class="px-3" role="button" data-toggle="modal" data-target="#SyncLogsReport" aria-pressed="true">View Sync Logs</span>
                            </div>
                        </a>

                        <div class="dropdown-divider"></div>
                        @endif


                        @endif @if (auth()->user()->is_admin)

                        @if (\Route::current()->getName() == 'dashboard' || \Route::current()->getName() == 'outlet-dashboard')

                        <a class="dropdown-item" href="#">
                            <div class="row no-gutters align-items-center flex-nowrap"> 
                                <i class="icon icon-account-check"></i>
                                <span class="px-3" role="button" data-toggle="modal" data-target="#SearchWinResultAdmin" aria-pressed="true">Payout</span> 
                            </div>
                        </a>

                        <!-- <a class="dropdown-item" href="#">
                            <div class="row no-gutters align-items-center flex-nowrap"> 
                                <i class="icon icon-trash"></i>
                                <span class="px-3" role="button" data-toggle="modal" data-target="#CancelTicket" aria-pressed="true">Cancel Ticket</span> 
                            </div>
                        </a> -->

                        <div class="dropdown-divider"></div>
                        @endif
                        @if (\Route::current()->getName() == 'all-transactions')
                        <a class="dropdown-item" href="#">
                            <div class="row no-gutters align-items-center flex-nowrap"> 
                                <i class="icon icon-filter"></i>
                                <span class="px-3" role="button" data-toggle="modal" data-target="#FilterTransaction" aria-pressed="true">Filter Transaction</span> 
                            </div>
                        </a>

                        <a class="dropdown-item" href="{{ route('pdf-transactions', ['page' => $page]) }}{{ !is_null($query) ? '?' . $query : '' }}">
                            <div class="row no-gutters align-items-center flex-nowrap"> 
                                <i class="icon icon-file-pdf"></i>
                                <span class="px-3" role="button">Download as PDF</span> 
                            </div>
                        </a>

                        <div class="dropdown-divider"></div>
                        
                        @endif
                        
                        @if (auth()->user()->is_admin)
                        <a class="dropdown-item" href="#">
                            <div class="row no-gutters align-items-center flex-nowrap"> <i class="status icon-cloud-upload"></i> <span class="px-3" role="button" data-toggle="modal" data-target="#importTransactions" aria-pressed="true" >Import Transactions</span>
                            </div>
                        </a>

                        <div class="dropdown-divider"></div>

                        @endif

                        @endif

                        
                        
                        <a class="dropdown-item" href="#">
                            <div class="row no-gutters align-items-center flex-nowrap"> 
                                <i class="icon icon-account-settings-variant"></i>
                                <span class="px-3" role="button" data-toggle="modal" data-target="#Account-setting" aria-pressed="true">Account Setting</span> 
                            </div>
                        </a>

                        <a class="dropdown-item" href="{{ route('user-logout') }}" onclick="event.preventDefault();
                                                               document.getElementById('logout-form').submit();">
                            <div class="row no-gutters align-items-center flex-nowrap"> 
                                <i class="icon icon-logout"></i> 
                                <span class="px-3">Logout</span>

                            </div>
                        </a>
                        <form id="logout-form" action="{{ route('user-logout') }}" method="POST" style="display: none;"> {{ csrf_field() }} </form>
                    </div>

                </div>
                <div class="toolbar-separator"></div>

                <button type="button" class="quick-panel-button btn btn-icon" data-fuse-bar-toggle="quick-panel-sidebar"> <i class="icon icon-format-list-bulleted"></i> </button>

            </div>

        </div>

    </div>

</nav>

@endguest
