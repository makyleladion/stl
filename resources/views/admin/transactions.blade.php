@extends('layouts.main')

@section('content')

                    <!--Transactions Page starts-->                    
                    <section id="transactions-page">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-actions clearfix">
                                    <div class="float-left">
                                        <div class="content-header">Transactions Page</div>
                                        <p class="content-sub-header">Total transactions: 774446</p>
                                    </div>
                                    <div class="float-right">
                                        <div class="my-4 pr-3">
                                            <a href="#" class="py-1 mr-2 h6" data-toggle="modal" data-target="#filterTransaction"><i class="ft-search font-medium-5 mr-2"></i><span>Filter Transaction</span></a></li>
                                            <a href="#" class="py-1 h6"><i class="ft-download font-medium-5 mr-2"></i><span>Download PDF</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>                        
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="form-actions clearfix">
                                            <div class="float-left"><h4 class="card-title">Today's Transactions: <small>20,000</small></h4></div>
                                            <div class="float-right"><input type="text" class="form-control" id="basicInput" placeholder="Search by Ticket ID"></div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <table class="table table-responsive-lg text-left">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>TXN ID</th>
                                                        <th>Ticket ID</th>
                                                        <th>Outlet</th>
                                                        <th>Bet Numbers</th>
                                                        <th>Valid Amount</th>
                                                        <th>Bet Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($transactions as $transaction)
                                                    <tr>
                                                        <td>{{ $transaction->id(true) }}</td>
                                                        <td>{!! $transaction->tickets(true) !!}</td>
                                                        <td>{{ $transaction->outletName() }}</td>
                                                        <td>({{ $transaction->numberOfBets() }}) {!! $transaction->betsString() !!}</td>
                                                        <td>&#8369; {{ number_format($transaction->amount(), 2, '.', ',') }}</td>
                                                        <td>{{ $transaction->transactionDateTime()->toDayDateTimeString() }}</td>
                                                        <td>{{ $transaction->getDrawDateTimes() }}</td>
                                                        <td>
                                                            <a href="{{ route('single-transaction', ['transaction_id' => $transaction->getTransaction()->id, 'outlet_id' => $transaction->getOutlet()->id]) }}" class="info p-0" data-toggle="tooltip" data-placement="top" title="View Transaction" data-trigger="hover"">
                                                                <i class="ft-file-text font-large-1 mr-2"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="row text-justify">
                                                <div class="col-sm-12 col-md-7">
                                                    <div class="" id="" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div>
                                                </div>
                                                <div class="col-sm-12 col-md-5">
                                                    <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
                                                        <ul class="pagination">
                                                            <li class="paginate_button page-item previous disabled"><a href="#"tabindex="0" class="page-link">Previous</a></li>
                                                            <li class="paginate_button page-item active"><a href="#" tabindex="0" class="page-link">1</a></li>
                                                            <li class="paginate_button page-item "><a href="#" tabindex="0" class="page-link">2</a></li>
                                                            <li class="paginate_button page-item "><a href="#" tabindex="0" class="page-link">3</a></li>
                                                            <li class="paginate_button page-item "><a href="#" tabindex="0" class="page-link">4</a></li>
                                                            <li class="paginate_button page-item next" id="DataTables_Table_0_next"><a href="#" tabindex="0" class="page-link">Next</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!--Extended Table Ends-->                   




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
@endsection
