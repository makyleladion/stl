@extends('layouts.main') @section('content')
<div id="project-dashboard" class="page-layout blank p-6">
    <div class="page-content-wrapper">
        <div class="page-content">

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
            <div class="tab-content">

							@if (!auth()->user()->is_read_only)
              <div class="select_wrapper">
                <select class="options" id="origin-input">
                  <option value="">All</option>
                  @foreach ($origin as $k => $o)
                  <option value="{{ $k }}"{{ $current_origin == $k ? ' selected' : ''}}>{{ ucfirst($o) }}</option>
                  @endforeach
                </select>
              </div>
              @endif
                <div class="tab-pane fade show active" id="budget-summary-tab-pane" role="tabpanel" aria-labelledby="budget-summary-tab">
                    <div class="widget-group row">

                        <!-- WIDGET 1 -->
                        @foreach (\App\System\Data\Timeslot::drawTimeslots() as $sched_key => $timeslot)
                        <div class="col-12 col-sm-6 col-xl-6 p-4">

                            <div class="widget widget1 card">

                                <div class="widget-header pl-4 pr-2 row no-gutters align-items-center justify-content-between">

                                    <div class="col">

                                        <span class="h4">{{ date('g:ia', strtotime($timeslot)) }}</span>

                                    </div>
                                    @if (date('Y-m-d') == $current_drawdate)
                                    <button type="button" class="btn btn-icon btn-fab btn-sm fuse-ripple-ready winning-result-modal" data-toggle="modal" data-target="#WinningResult" data-schedule="{{ $timeslot }}">
                                        <i class="icon icon-update"></i>
                                    </button>
                                    @endif

                                </div>

                                <div class="widget-content pt-2 pb-8 d-flex flex-column align-items-center justify-content-center">
                                    <div class="title text-secondary"> &#8369; <span id="put-{{$sched_key}}">{{ number_format($daily_sales[$sched_key], 0, '.', ',') }}</span> </div>
                                    <div class="sub-title h6 text-muted">Draw Sales</div>
                                </div>

                                <div class="widget-footer p-4 bg-light row no-gutters align-items-center">
                                    @foreach ($winnings[$sched_key] as $game => $result)
                                    <span class="text-muted">{{ $game }}:</span>
                                    <span class="ml-2" id="">{{ str_replace(':','-',$result) }}</span>
                                    <span class="ml-5"></span> @endforeach
                                    <span class="text-muted">No. of tickets:</span>
                                    <span class="ml-2" id="put-tickets-{{$sched_key}}">{{ $number_of_tickets[$sched_key] }}</span>
                                    <span class="ml-5"></span>
                                    <span class="text-muted">No. of Winners:</span>
                                    <span class="ml-2" id="">{{ $number_of_winnings[$sched_key] }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        <div class="col-12 col-sm-6 col-xl-6 p-4">

                            <div class="widget widget1 card">

                                <div class="widget-header pl-4 pr-2 row no-gutters align-items-center justify-content-between">

                                    <div class="col">

                                        <span class="h4">Total Sales</span>

                                    </div>

                                </div>

                                <div class="widget-content pt-2 pb-8 d-flex flex-column align-items-center justify-content-center">
                                    <div class="title text-secondary"> &#8369; <span id="put-total_amount">{{ number_format($total_amount, 0, '.', ',') }}</span> </div>
                                    <div class="sub-title h6 text-muted">Draw Sales</div>
                                </div>

                                <div class="widget-footer p-4 bg-light row no-gutters align-items-center">
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-6 col-xl-12 p-4">
                            <div class="widget widget10 card">

                                <div class="widget-header pl-4 pr-2 row no-gutters align-items-center justify-content-between">

                                    <div class="col">
                                        <span class="h5">Total Sales Per Game</span>
                                    </div>

                                    <button type="button" class="btn btn-icon fuse-ripple-ready">
                                        <i class="icon icon-dots-vertical"></i>
                                    </button>

                                </div>

                                <div class="widget-content p-4">

                                    <table class="table table-responsive table-striped">
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
                                                <td><span>{{ number_format($gameSale, 2, '.', ',') }}</span></td>
                                                @endforeach
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <th scope="row">Total</th>
                                                @foreach($daily_aggregated_sales_totals as $gameSaleTotal) 
                                                <td><span>{{ number_format($gameSaleTotal, 2, '.', ',') }}</span></td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                        <!-- / WIDGET 2 -->
                        
                        <div class="col-12 col-sm-6 col-xl-12 p-4">
                            <div class="widget widget10 card">

                                <div class="widget-header pl-4 pr-2 row no-gutters align-items-center justify-content-between">

                                    <div class="col">
                                        <span class="h4">Today's Winners (PHP {{ number_format($winning_allocation, 2, '.', ',') }})</span>
                                    </div>

                                    <button type="button" class="btn btn-icon fuse-ripple-ready">
                                        <i class="icon icon-dots-vertical"></i>
                                    </button>

                                </div>

                                @foreach($winners_aggregated as $key => $drawTimeData) 

                                <div class="widget-header pl-4 pr-2 row no-gutters align-items-center justify-content-between">

                                <div class="col">
                                    <span class="h5">{{ date('g A', strtotime($key)) }} Winners (PHP {{ number_format($drawTimeData['total_amount'], 2, '.', ',') }})</span>
                                </div>

                                </div>

                                <div class="widget-content p-4">

                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th>Ticket No.</th>
                                                <th>Outlet</th>
                                                <th>Teller</th>
                                                <th>Customer</th>
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
                                                <td><span class="">{{ $winner->ticketNumber() }}</span></td>
                                                <td><span class="">{{ $winner->outletName() }}</span></td>
                                                <td><span>{{ $winner->teller() }}</span></td>
                                                <td><span>{{ $winner->customer() }}</span></td>
                                                <td><span>{{ $winner->bet() }}</span></td>
                                                <td><span>{{ $winner->game() }}</span></td>
                                                <td><span>{{ $winner->type() }}</span></td>
                                                <td><span>PHP {{ number_format($winner->amount(), 2, '.', ',') }}</span></td>
                                                <td><span>PHP {{ number_format($winner->winningPrize(), 2, '.', ',') }}</span></td>
                                                <td><span>{{ $winner->drawDateTimeCarbon()->toDayDateTimeString() }}</span></td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endforeach
                            </div>

                        </div>
                        <!-- / WIDGET 1 -->
                        
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
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
	var initTickets = {!! json_encode($number_of_tickets) !!};
	var totalAmount = {{ $total_amount }};
	var app_key = "{{ $app_key }}";
	var app_cluster = "{{ $app_cluster }}";
	var allowed_ids = {!! json_encode($allowed_ids) !!};
	var is_superadmin = {{ ($is_superadmin) ? 'true' : 'false' }};
	
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
			initTickets[data.bet_schedule] += data.ticket_count;
			totalAmount += data.amount;
			
			$('#put-' + data.bet_schedule).html(number_format(initData[data.bet_schedule], 0, '.', ','));
			$('#put-tickets-' + data.bet_schedule).html(initTickets[data.bet_schedule]);
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
