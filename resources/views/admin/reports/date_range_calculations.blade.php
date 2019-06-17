@extends('layouts.main') @section('content')

<div class="page-layout carded full-width">

    <div class="top-bg bg-secondary"></div>

    <!-- CONTENT -->
    <div class="page-content">

        <!-- HEADER -->
        <div class="header bg-secondary text-auto row no-gutters align-items-center justify-content-between">

            <!-- APP TITLE -->
            <div class="col-12 col-sm">

                <div class="logo row no-gutters align-items-start">

                    <div class="logo-icon mr-3 mt-1">
                        <i class="icon-account-multiple s-6"></i>
                    </div>

                    <div class="logo-text">
                        <div class="h4">Sales Calculations From <b>{{ date('l, F j, Y', strtotime($date_from)) }}</b> to <b>{{ date('l, F j, Y', strtotime($date_to)) }}</b>.</div>
                    </div>

                </div>
            </div>
            <!-- / APP TITLE -->
        </div>
        <!-- / HEADER -->

        <div class="page-content-card">
            <!-- CONTENT TOOLBAR -->
            <div class="toolbar p-4">

                <div class="row">

                    <div class="col-sm-3" style="margin-top: 10px;">
                        <input type="text" id="date-from-machine-datepicker" class="h6 custom-select form-control" placeholder="Pick a Date" value="{{$date_from}}">
                        <script>
                            $(document).ready(function() {
                                $('#date-from-machine-datepicker').datepicker({format:'yyyy-mm-dd'});
                                $('#date-from-machine-datepicker').change(function() {
                                    var dateFrom = $(this).val();
                                    var dateTo = $('#date-to-machine-datepicker').val();
                                    var url = "{{ route(\Route::current()->getName()) }}/{{$page}}/" + dateFrom + "/" + dateTo;

                                    var dateFromTimestamp = new Date(dateFrom).getTime();
                                    var dateToTimestamp = new Date(dateTo).getTime();

                                    if (dateFromTimestamp > dateToTimestamp) {
                                        alert('Please select a Date From value lower than the Date To.');
                                    } else {
                                    	window.location.href = url;
                                    }
                                });
                            });
                            </script>
                    </div>
                    
                    <div class="col-sm-3" style="margin-top: 10px;">
                        <input type="text" id="date-to-machine-datepicker" class="h6 custom-select form-control" placeholder="Pick a Date" value="{{$date_to}}">
                        <script>
                            $(document).ready(function() {
                                $('#date-to-machine-datepicker').datepicker({format:'yyyy-mm-dd'});
                                $('#date-to-machine-datepicker').change(function() {
                                		var dateFrom = $('#date-from-machine-datepicker').val();
                                    var dateTo = $(this).val();
                                    var url = "{{ route(\Route::current()->getName()) }}/{{$page}}/" + dateFrom + "/" + dateTo;

                                    var dateFromTimestamp = new Date(dateFrom).getTime();
                                    var dateToTimestamp = new Date(dateTo).getTime();

                                    if (dateFromTimestamp > dateToTimestamp) {
                                        alert('Please select a Date From value that is lower than the Date To.');
                                    } else {
                                    	window.location.href = url;
                                    }
                                });
                            });
                            </script>
                    </div>
                    
                    @if (!auth()->user()->is_read_only)
                    <div class="col-sm-2" style="margin-top: 10px;">
                    	<select class="form-control" id="origin-input-summary">
                    		<option value="">All</option>
                    		@foreach ($origin as $k => $o)
                        <option value="{{ $k }}"{{ $current_origin == $k ? ' selected' : ''}}>{{ ucfirst($o) }}</option>
                        @endforeach
                    	</select>
                    </div>
                    
                    <div class="col-sm-3" style="margin-top: 13px;">
                    	<input type="checkbox" id="is-usher" value="true"{{ $is_usher ? ' checked' : '' }}>
                      <label for="is-usher">Show Ushers Only</label>
                    </div>
                    @endif
                    
                    <!-- <div class="col-sm-3" style="margin-top: 13px;">
                    	<input type="checkbox" id="is-usher" value="true"{{ $is_usher ? ' checked' : '' }}>
                      <label for="is-usher">Show Ushers Only</label>
                    </div> -->

                    <!-- <div class="col-3">
                        <button id="exportButton" class="btn btn-secondary btn-fab btn-sm" role="button" aria-pressed="true"><i class="icon icon-printer"></i></button>                        
                    </div>  -->

                </div>  

            </div>
            <!-- / CONTENT TOOLBAR -->

            <div id="e-commerce-orders-table_wrapper" class="dataTables_wrapper no-footer">
               <div class="dataTables_scroll">
                  <div class="dataTables_scrollBody">
                    <table id="e-commerce-orders-table" class="table table-hover dataTable">
                      <thead>
                          <tr>
                          		<th>
                                  <div class="table-header"><span class="column-title">Rank</span></div>
                              </th>
                              <th>
                                  <div class="table-header"><span class="column-title">Outlet Name</span></div>
                              </th>
                              <th>
                                  <div class="table-header"><span class="column-title">Teller</span></div>
                              </th>
                              @foreach (\App\System\Data\Timeslot::drawTimeslots() as $schedKey => $time)
                              <th>
                                  <div class="table-header"><span class="column-title">{{ date('g:ia', strtotime($time)) }}</span></div>
                              </th>
                              @endforeach
                              <th>
                                  <div class="table-header"><span class="column-title">Gross Sales</span></div>
                              </th>
                          </tr>
                      </thead>
                      <tbody>
                      	<?php $rank = 1; ?>
                      	<?php $ids = []; ?>
                        @foreach($outlets_summary as $summary)
                        	<?php $ids[] = $summary['outlet']->id(); ?>
                          <tr>
                          		<td>{{ $rank++ }}</td>                          
                              <td>{{ $summary['outlet']->name() }}</td>
                              <td>{{ $summary['outlet']->assignedTellers(true) }}</td>
                              @foreach (\App\System\Data\Timeslot::drawTimeslots() as $schedKey => $time)
                              <td>PHP {{ number_format($summary['sales'][$schedKey], 0, '.', ',') }}</td>
                              @endforeach
                              <td>
                              	@if ($current_origin === \App\System\Data\Transaction::TRANSACTION_ORIGIN_MOBILE)
                              	<div>
                              		<a data-toggle="collapse" {!! ($current_origin === \App\System\Data\Transaction::TRANSACTION_ORIGIN_MOBILE) ? 'class="sales-separation"' : '' !!} href="#sales-{{ $summary['outlet']->id() }}" role="button" aria-expanded="false" aria-controls="sales-{{ $rank }}">
                              			<b>PHP {{ number_format($summary['sales']['total'], 0, '.', ',') }}</b>
                              		</a>
                              	</div>
                              	<div class="collapse" id="sales-{{ $summary['outlet']->id() }}" data-outlet="{{ $summary['outlet']->id() }}">
                                  <div class="card card-body" id="sales-separation-content-{{ $summary['outlet']->id() }}">
                                  </div>
                              	</div>
                              	@else
                              		<b>PHP {{ number_format($summary['sales']['total'], 0, '.', ',') }}</b>
                              	@endif
                              </td>
                          </tr>
                        @endforeach
                      </tbody>
                      <tfoot>
                      		<tr>
                      				<th></th>
                      				<th></th>
                      				<th>Totals:</th>
                      				@foreach (\App\System\Data\Timeslot::drawTimeslots() as $schedKey => $time)
                              <th>PHP {{ number_format($totals[$schedKey], 0, '.', ',') }}</th>
                              @endforeach
                              <th>PHP {{ number_format($totals['overall'], 0, '.', ',') }}</th>
                          </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
                <div class="dataTables_info" id="e-commerce-orders-table_info" role="status" aria-live="polite">
                      <div id="pagi_page-content" class="pagi_page-content">Page {{ $page }} of {{$total_pages}}</div>
                </div>
                <div class="dataTables_paginate paging_simple_numbers" id="e-commerce-orders-table_paginate">
                    <a href="{{ route('reports-date-range-calculations', ['page' => $prev, 'date_from' => $date_from, 'date_to' => $date_to]) }}" class="paginate_button previous" id="e-commerce-orders-table_previous">Previous</a> 
                    @for ($i = 1; $i <= $total_pages; $i++) 
                    <a href="{{ route('reports-date-range-calculations', ['page' => $i, 'date_from' => $date_from, 'date_to' => $date_to]) }}" class="paginate_button current" aria-controls="e-commerce-orders-table" data-dt-idx="{{ $i }}" tabindex="0">{{ $i }}</a>
                    @endfor
                    <a href="{{ route('reports-date-range-calculations', ['page' => $next, 'date_from' => $date_from, 'date_to' => $date_to]) }}" class="paginate_button next" aria-controls="e-commerce-orders-table" data-dt-idx="4" tabindex="0" id="e-commerce-orders-table_next">Next</a>                  
                </div>
            </div>

        </div>
    </div>
    <!-- / CONTENT -->
</div>
@if (!auth()->user()->is_read_only)
<script type="text/javascript">
$(document).ready(function() {
	$('#origin-input-summary').change(function() {
		var v = $(this).val();
		window.location.href = '{{ route('reports-date-range-calculations', ['date_from' => $date_from, 'date_to' => $date_to, 'page' => $page]) }}?origin=' + v;
	});
});
</script>
@endif

@if ($current_origin === \App\System\Data\Transaction::TRANSACTION_ORIGIN_MOBILE)
<<script type="text/javascript">
$(document).ready(function() {
	var ids = {{ json_encode($ids) }};
	var data = [];

	for (var i in ids) {
		$('#sales-' + ids[i]).on('show.bs.collapse', function() {

			var id = $(this).data('outlet');
			var is_usher = $('#is-usher').is(':checked');
			if (typeof data[id] == 'undefined') {
				$.ajax({
					url: '{{ route('separate-sales-mobile') }}',
			    type: 'post',
			    data: { 'id' : id , 'timestamp' : '{{ date('Y-m-d h:i:s') }}', 'date_from' : '{{ $date_from }}', 'date_to' : '{{ $date_to }}', 'is_usher' : is_usher },
			    headers: {
			        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			    },
			    dataType: 'json',
			    cache: false,
			    success: function(output) {
				    console.log(output);
						data[id] = output.sales;
						var html = '<ul>';
						for (var n in data[id]) {
							html += '<li><div><b>' + data[id][n]['teller_name'] + '</b></div><div>PHP ' + data[id][n]['bet_amount'] + '</div></li>';
						}
						html += '</ul>';
						$('#sales-separation-content-' + id).html(html);
			    },
			    error: function(e) {
						alert(e.responseJSON.message);
			    }
				});
			} else {
				var html = '<ul>';
				for (var n in data[id]) {
					html += '<li>PHP ' + data[id][n]['bet_amount'] + '</li>';
				}
				html += '</ul>';
				$('#sales-separation-content-' + id).html(html);
			}
		});
	}
});
</script>
@endif
<<script type="text/javascript">
$(document).ready(function() {
	$('#is-usher').change(function() {
		var isUsher = $(this).is(':checked');
		var url = "{{ route('reports-date-range-calculations', ['page' => $page, 'date_from' => $date_from, 'date_to' => $date_to, 'origin' => 'id_mobile_usher']) }}";
		if (isUsher) {
			url += "&is_usher=true";
		} else {
			url += "&is_usher=false";
		}

		window.location.href = url;
	});
});
</script>

@endsection
