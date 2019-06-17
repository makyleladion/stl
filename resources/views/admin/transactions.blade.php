@extends('layouts.main')

@section('content')

<div class="page-layout carded full-width">

    <div class="top-bg bg-secondary"></div>

    <!-- CONTENT -->
    <div class="page-content">

        <!-- HEADER -->
        <div class="header bg-secondary text-auto row no-gutters align-items-center justify-content-between">

            <!-- APP TITLE -->
            <div class="col-12 col-sm">
            
            		@if (\Session::has('error-flash'))
                <div class="alert alert-danger" role="alert">{{ session('error-flash') }}</div>
                @endif

                <div class="logo row no-gutters align-items-start">

                    <div class="logo-icon mr-3 mt-1">
                        <i class="icon-cards-outline s-6"></i>
                    </div>

                    <div class="logo-text">
                        <div class="h4">Transactions</div>
                        <div class="">Total Transactions: <span id="total-transactions-container">{{ $total_transactions }}</span></div>
                    </div>

                </div>
            </div>
            <!-- / APP TITLE -->

        </div>
        <!-- / HEADER -->

        <div class="page-content-card">

            <div id="e-commerce-orders-table_wrapper" class="dataTables_wrapper no-footer">
                <div class="dataTables_scroll">

                    <div class="dataTables_scrollBody">
                         <table id="e-commerce-orders-table" class="table dataTable">

                                <thead>

                                    <tr>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">ID</span></div>
                                        </th>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Transaction ID</span>
                                            </div>
                                        </th>
                                        
                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Ticket ID</span>
                                            </div>
                                        </th>

                                        <th>
                                          <div class="table-header">
                                            <span class="column-title">Outlet</span>
                                          </div>
                                        </th>

                                        <th>
                                          <div class="table-header">
                                            <span class="column-title">Teller</span>
                                          </div>
                                        </th>

                                        <th>
                                          <div class="table-header">
                                            <span class="column-title">Game</span>
                                          </div>
                                        </th>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Bets</span>
                                            </div>
                                        </th>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">No. of Bets</span>
                                            </div>
                                        </th>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Valid Amount</span>
                                            </div>
                                        </th>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Date</span>
                                            </div>
                                        </th>
                                        
                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Draw Date/Time</span>
                                            </div>
                                        </th>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Actions</span></div>
                                        </th>

                                    </tr>
                                </thead>

                            <tbody id="transactions-container">
                                @foreach ($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id(true) }}</td>
                                    <td>{{ $transaction->transactionNumber() }}</td>
                                    <td>{!! $transaction->tickets(true) !!}</td>
                                    <td><a href="{{ route('outlet-dashboard', ['outlet_id' => $transaction->getOutlet()->id]) }}">{{ $transaction->outletName() }}</a></td>
                                    <td><a href="{{ route('edit-user', ['user_id' => $transaction->tellerObj()->id]) }}">{{ $transaction->teller() }}</a></td>
                                    <td>{{ $transaction->getBetGameLabels(true) }}</td>
                                    <td>{!! $transaction->betsString() !!}</td>
                                    <td>{{ $transaction->numberOfBets() }}</td>
                                    <td>PHP {{ number_format($transaction->amount(), 2, '.', ',') }}</td>
                                    <td>{{ $transaction->transactionDateTime()->toDayDateTimeString() }}</td>
                                    <td>{{ $transaction->getDrawDateTimes() }}</td>
                                    <td>
                                        <a href="{{ route('single-transaction', ['transaction_id' => $transaction->getTransaction()->id, 'outlet_id' => $transaction->getOutlet()->id]) }}" class="btn btn-default btn-sm">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                         </table>
                         <center><div class="delpagination" >
                            {{ $raw_transactions->links() }}
                        </div></center>         
                    </div>
                </div>
                
            </div>
            
        </div>
    </div>
    <!-- / CONTENT -->
</div>

<script type="text/javascript" src="{{ url('/assets/js/apps/e-commerce/orders/orders.js?v=1')}}"></script>
@if (intval($page) == 1 && empty($query))
<script type="text/javascript" src="https://js.pusher.com/4.3/pusher.min.js"></script>
@endif

@if (!empty($query))
<script>
$(document).ready(function() {
	$('.delpagination a').each(function() {
		var href = $(this).attr('href');
		href += ((href.indexOf('page') !== -1) ? '&' : '?') + '{!! $query !!}';
		$(this).attr('href', href);
	});
});
</script>
@endif

@if (intval($page) == 1 && empty($query))
<script type="text/javascript">
$(document).ready(function() {

	var initTotalTransactions = {{ $total_transactions }};
	
	var app_key = "{{ env('PUSHER_APP_KEY') }}";
	var app_cluster = "{{ env('PUSHER_APP_CLUSTER') }}";
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

	var channel = pusher.subscribe('private-transactions-broadcast');
	channel.bind('transactions-broadcast.per-transaction', function(data) {

		var inArray = function(needle, haystack) {
			var length = haystack.length;
			for(var i = 0; i < length; i++) {
				if(haystack[i] == needle) return true;
			}
			return false;
		}

		if (is_superadmin || inArray(data.user_id, allowed_ids)) {
    		initTotalTransactions++;
    		$('#transactions-container').prepend(data.transaction);
    		$('#total-transactions-container').html(initTotalTransactions);
    		setTimeout(function(){
    			$('#transaction-' + data.id).removeClass('table-success');
    		}, 3000);
		}
	});
});
</script>
@endif

@include('inc.winningResult')
@endsection
