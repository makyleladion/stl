@extends('layouts.main') 

@section('content')

										@if (\Session::has('dashboard-success'))
                    <div class="alert alert-success" role="alert">{{ session('dashboard-success') }}</div>
                    @endif @if (\Session::has('error-flash'))
                    <div class="alert alert-danger" role="alert">{{ session('error-flash') }}</div>
                    @endif @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-12 mt-1 mb-1 mx-1">
                            <div class="form-actions clearfix">
                                <div class="float-left">
                                     <div class="content-header"><h2>Howdy, {{ Auth::user()->name }}!</h2></div>
                                    <p class="content-sub-header">Welcome to Dashboard.</p>
                                </div>
                                <div class="float-right">
                                    <div class="my-4 pr-3">
                                    <a href="#" class="py-1 h6" data-toggle="modal" data-target="#filterDashboard"><i class="ft-search font-medium-5 mr-2"></i><span>Filter Date</span></a></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <section id="minimal-statistics">
                        <div class="row" matchHeight="card">
                            @foreach (\App\System\Data\Timeslot::drawTimeslots() as $sched_key => $timeslot)
                            <div class="col-xl-4 col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="px-3 py-3">
                                            <div class="media">
                                                <div class="media-body text-left">
                                                    <h3 class="mb-1 primary">&#8369; <span id="put-{{$sched_key}}">{{ number_format($daily_sales[$sched_key], 0, '.', ',') }}</span></h3>
                                                    <span>Draw Sales {{ date('g:ia', strtotime($timeslot)) }}</span>
                                                </div>
                                                <div class="media-right align-self-center">
                                                    <i class="ft-bar-chart primary font-large-2 float-right"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach                            
                        </div>                    

                        <div class="row" matchHeight="card">
                            <div class="col-xl-4 col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="px-3 py-3">
                                            <div class="media">
                                                <div class="media-body text-left">
                                                    <h3 class="mb-1 primary">&#8369; <span id="put-total_amount">{{ number_format($total_amount, 0, '.', ',') }}</h3>
                                                    <span>Total Sales</span>
                                                </div>
                                                <div class="media-right align-self-center">
                                                    <i class="ft-bar-chart-2 primary font-large-2 float-right"></i>
                                                </div>
                                            </div>
                                            <div class="mt-2 mb-0">
                                                <ngb-progressbar type="primary" [value]="80" class="progress-bar-sm"></ngb-progressbar>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="px-3 py-3">
                                            <div class="media">
                                                <div class="media-body text-left">
                                                    <h3 class="mb-1 warning">{{ number_format($total_number_of_winnings, 0, '.', ',') }}</h3>
                                                    <span>Total Winners</span>
                                                </div>
                                                <div class="media-right align-self-center">
                                                    <i class="ft-users warning font-large-2 float-right"></i>
                                                </div>
                                            </div>
                                            <div class="mt-2 mb-0">
                                                <ngb-progressbar type="warning" [value]="35" class="progress-bar-sm"></ngb-progressbar>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="col-xl-3 col-lg-6 col-12">
                                <a href="payouts.php"><div class="card">
                                    <div class="card-body">
                                        <div class="px-3 py-3">
                                            <div class="media">
                                                <div class="media-body text-left">
                                                    <h3 class="mb-1 success">64.89 %</h3>
                                                    <span>Total Payouts</span>
                                                </div>
                                                <div class="media-right align-self-center">
                                                    <i class="ft-user-check success font-large-2 float-right"></i>
                                                </div>
                                            </div>
                                            <div class="mt-2 mb-0">
                                                <ngb-progressbar type="success" [value]="60" class="progress-bar-sm"></ngb-progressbar>
                                            </div>
                                        </div>
                                    </div>
                                </div></a>
                            </div> -->
                            <div class="col-xl-4 col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="px-3 py-3">
                                            <div class="media">
                                                <div class="media-body text-left">
                                                    <h3 class="mb-1 danger">{{ number_format($total_number_of_tickets, 0, '.', ',') }}</h3>
                                                    <span>Total Tickets</span>
                                                </div>
                                                <div class="media-right align-self-center">
                                                    <i class="ft-file-text danger font-large-2 float-right"></i>
                                                </div>
                                            </div>
                                            <div class="mt-2 mb-0">
                                                <ngb-progressbar type="danger" [value]="40" class="progress-bar-sm"></ngb-progressbar>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row match-height">
                            <div class="col-xl-4 col-lg-5 col-md-5 col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Earnings</h4>
                                        <!-- <span class="grey">Mon 18 - Sun 21</span> -->
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <div class="earning-details mb-4">
                                                <h3 class="font-large-1 mb-1">&#8369; {{ number_format($earnings, 2, '.', ',') }} <i class="ft-arrow-up font-large-1 teal accent-3"></i></h3>
                                                <span class="font-medium-1 grey d-block">Gross Sales - Payouts</span>
                                            </div>
                                            @if (!auth()->user()->is_read_only)
                                            <div class="earning-details mb-4">
                                                <h3 class="font-large-1 mb-1">&#8369; {{ number_format($remittance, 2, '.', ',') }} <i class="ft-arrow-up font-large-1 teal accent-3"></i></h3>
                                                <span class="font-medium-1 grey d-block">Remittance</span>
                                            </div>
                                            <div class="earning-details mb-4">
                                                <h3 class="font-large-1 mb-1">&#8369; {{ number_format($our_commission, 2, '.', ',') }} <i class="ft-arrow-up font-large-1 teal accent-3"></i></h3>
                                                <span class="font-medium-1 grey d-block">Commission</span>
                                            </div>
                                            @endif
                                            <!-- <div class="action-buttons mt-4 mb-1 text-center">
                                                <a class="btn btn-raised gradient-blackberry py-2 px-3 white mr-2">View Full</a>
                                                <a class="btn btn-raised btn-outline-grey py-2 px-3">Print</a>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-xl-8 col-lg-7 col-md-7 col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="media">
                                            <div class="media-body text-left">
                                                <h3 class="font-large-1 mb-0">Results</h3>
                                                <span class="grey"></span>
                                            </div>
                                            <div class="media-right text-right">
                                                <a href="#" class="h6" data-toggle="modal" data-target="#inputWinningResults"><i class="icon-plus font-large-1 mr-1 mb-1"></i></a></li>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <table class="table table-responsive-lg text-left">
                                                <thead>
                                                    <tr>
                                                        <th>Game Type</th>
                                                        @foreach (\App\System\Data\Timeslot::drawTimeslots() as $sched_key => $timeslot)
                                                        <th>{{ date('g:ia', strtotime($timeslot)) }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach(\App\System\games\GamesFactory::getGames() as $game) 
                                                		<tr>
                                                        <th scope="row">{{ $game->label() }}</th>
                                                        @foreach (\App\System\Data\Timeslot::drawTimeslots() as $sched_key => $timeslot)
                                                        			<td>{{ (empty($winnings[$sched_key][$game->label()])) ? '-' : $winnings[$sched_key][$game->label()] }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row match-height">
                            <div class="col-xl-4 col-lg-5 col-md-5 col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">Payouts</h4>
                                        <!-- <span class="grey">Mon 18 - Sun 21</span> -->
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <div class="earning-details mb-4">
                                                <h3 class="font-large-1 mb-1">&#8369; {{ number_format($winning_allocation, 2, '.', ',') }} <i class="ft-arrow-up font-large-1 teal accent-3"></i></h3>
                                                <span class="font-medium-1 grey d-block">Total Payouts</span>
                                            </div>
                                            <!-- <div class="action-buttons mt-4 mb-1 text-center">
                                                <a class="btn btn-raised gradient-blackberry py-2 px-3 white mr-2">View Full</a>
                                                <a class="btn btn-raised btn-outline-grey py-2 px-3">Print</a>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-8 col-lg-7 col-md-7 col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="media">
                                            <div class="media-body text-left">
                                                <h3 class="font-large-1 mb-0">Sales Per Game</h3>
                                                <span class="grey"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <table class="table table-responsive-lg text-left">
                                                <thead>
                                                    <tr>
                                                        <th>Draw Time</th>
                                                        @foreach($daily_aggregated_sales_headers as $gameLabel) 
                                                        <th>{{ $gameLabel }}</th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($daily_aggregated_sales as $das)
                                                    <tr>
                                                        <th scope="row">{{ $das['drawtime'] }}</th>
                                                        @foreach($das['games'] as $gameSale) 
                                                        <td>&#8369; {{ number_format($gameSale, 2, '.', ',') }}</td>
                                                        @endforeach
                                                    </tr>
                                                    @endforeach
                                                    <tr>
                                                        <th scope="row">Total</th>
                                                        @foreach($daily_aggregated_sales_totals as $gameSaleTotal) 
                                                        <td>&#8369; {{ number_format($gameSaleTotal, 2, '.', ',') }}</td>
                                                        @endforeach
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>   

                    <section id="extended">       
                        <div class="row">
                            <div class="col-12 mx-1 my-2">
                                <div class="content-header">Today's Winner</div>
                                <p class="content-sub-header">Total amount: &#8369; {{ number_format($winning_allocation, 2, '.', ',') }}</p>
                            </div>
                        </div>     

												@foreach($winners_aggregated as $key => $drawTimeData)
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card collapse-icon accordion-icon-rotate">
                                    <div class="card-header" id="headingCollapse1">
                                        <h4>{{ date('g A', strtotime($key)) }} Winners (&#8369; {{ number_format($drawTimeData['total_amount'], 2, '.', ',') }})</h4>
                                    </div>
                                    <div id="collapse1" role="tabpanel" aria-labelledby="headingCollapse1" class="collapse show">
                                        <div class="card-body">
                                            <div class="card-block">
                                                <table class="table table-responsive-md text-left">
                                                    <thead>
                                                        <tr>
                                                            <th>Ticket No.</th>
                                                            <th>Outlet</th>
                                                            <th>Teller</th>
                                                            <th>Bet</th>
                                                            <th>Game</th>
                                                            <th>Type</th>
                                                            <th>Amount</th>
                                                            <th>Win Amt</th>
                                                            <th>Draw Date</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    		@foreach ($drawTimeData['winners'] as $winner)
                                                        <tr>
                                                            <td><span>{{ $winner->ticketNumber() }}</span></td>
                                                            <td><span>{{ $winner->outletName() }}</span></td>
                                                            <td><span>{{ $winner->teller() }}</span></td>
                                                            <td><span>{{ $winner->bet() }}</span></td>
                                                            <td><span>{{ $winner->game() }}</span></td>
                                                            <td><span>{{ $winner->type() }}</span></td>
                                                            <td><span>&#8369; {{ number_format($winner->amount(), 2, '.', ',') }}</span></td>
                                                            <td><span>&#8369; {{ number_format($winner->winningPrize(), 2, '.', ',') }}</span></td>
                                                            <td><span>{{ $winner->drawDateTimeCarbon()->toDayDateTimeString() }}</span></td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <!-- / WIDGET 1 -->
                        
                    </section>
                        
<!--                     </div>
                </div>

            </div>
        </div>
    </div>
</div> -->
<script type="text/javascript" src="{{url('/assets/js/apps/dashboard/project.js')}}"></script>
<script type="text/javascript" src="https://js.pusher.com/4.3/pusher.min.js"></script>
@if (!auth()->user()->is_read_only)
<script type="text/javascript">
$(document).ready(function() {
	$('#origin-input').change(function() {
		var v = $(this).val();
		window.location.href = '{{ route('dashboard', ['draw_date' => $current_drawdate]) }}?origin=' + v;
	});
});
</script>
@endif
@if ($is_today)
<script type="text/javascript">
$(document).ready(function() {
	var initData = {!! json_encode($daily_sales) !!};
	var totalAmount = {{ $total_amount }};
	var app_key = "{{ $app_key }}";
	var app_cluster = "{{ $app_cluster }}";
	var allowed_ids = {!! json_encode($allowed_ids) !!};
	var is_superadmin = {{ ($is_superadmin) ? 'true' : 'false' }};

	var initTotalTickets = {{ $total_number_of_tickets }};
	
	var pusher = new Pusher(app_key, {
		cluster: app_cluster,
	  forceTLS: true,
	  authEndpoint: '{{ url('/broadcasting/auth') }}',
	  auth: {
	  	headers: {
	    	'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
	    }
	  }
	});

	var channel = pusher.subscribe('private-sales-broadcast');
	channel.bind('sales-broadcast.calculated', function(data) {
		
		var inArray = function(needle, haystack) {
			var length = haystack.length;
			for(var i = 0; i < length; i++) {
				if(haystack[i] == needle) return true;
			}
			return false;
		}
		
		if (is_superadmin || inArray(data.user.id, allowed_ids)) {
			initData[data.bet_schedule] += data.amount;
			totalAmount += data.amount;

			initTotalTickets  += data.ticket_count;
			
			$('#put-' + data.bet_schedule).html(number_format(initData[data.bet_schedule], 0, '.', ','));
			$('#put-total_amount').html(number_format(totalAmount, 0, '.', ','));
		}
		
	});
});
</script>
@endif
@include('inc.winningResult') 
@include('inc.modals-admin')
@include('inc.modal-forms')
@endsection
