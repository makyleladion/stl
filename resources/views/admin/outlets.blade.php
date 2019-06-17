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
                        <i class="icon-home-outline s-6"></i>
                    </div>
                    <div class="logo-text">
                        <div class="h4">Outlets</div>
                        <div class="">Total Outlets: {{ $total_outlets }}</div>
                    </div>
                </div>

            </div>
            <!-- / APP TITLE -->

            <!-- SEARCH -->
            <div class="col search-wrapper px-2">

                <div class="input-group">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-icon">
                            <i class="icon icon-magnify"></i>
                        </button>
                    </span>
                    <input id="products-search-input" type="text" class="form-control" placeholder="Search" aria-label="Search" />
                </div>

            </div>
            <!-- / SEARCH -->

            <div class="col-auto">
                <a href="{{route('new-outlet')}}" class="btn btn-secondary">ADD NEW OUTLET</a>
            </div>

        </div>
        <!-- / HEADER -->
        
        @if (\Session::has('outlet-success'))
        <div class="col-12 col-sm">
        	<div class="alert alert-success" role="alert">{{ session('outlet-success') }}</div>
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
        </div>
        @endif

        <div class="page-content-card">

            <div id="e-commerce-orders-table_wrapper" class="dataTables_wrapper no-footer">

                <div class="dataTables_scroll">

                    <div class="dataTables_scrollBody">
                        <table id="e-commerce-orders-table" class="table dataTable">

                            <thead>

                                <tr>

                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">ID</span>
                                        </div>
                                    </th>

                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Name</span>
                                        </div>
                                    </th>

                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Address</span>
                                        </div>
                                    </th>

                                    <th>
                                      <div class="table-header">
                                        <span class="column-title">Owner</span>
                                      </div>
                                    </th>
                                    
                                    <th>
                                      <div class="table-header">
                                        <span class="column-title">Assigned Tellers</span>
                                      </div>
                                    </th>
                                    
                                    <th>
                                      <div class="table-header">
                                        <span class="column-title">Is Affiliated</span>
                                      </div>
                                    </th>

                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Time-In</span>
                                        </div>
                                    </th>
                                    
                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Time-Out</span>
                                        </div>
                                    </th>

                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Actions</span>
                                        </div>
                                    </th>

                                </tr>
                            </thead>

                            <tbody>
                                @foreach($outlets as $outlet)
                                <tr>
                                    <td>{{ $outlet->id(true) }}</td>
                                    <td>{{ $outlet->name() }}</td>
                                    <td>{{ $outlet->address() }}</td>
                                    <td>{{ $outlet->owner() }}</td>
                                    <td>{{ $outlet->assignedTellers(true) }}</td>
                                    <td>{!! ($outlet->isAffiliated()) ? '<i class="icon icon-check"></i>' : '<i class="icon icon-file-excel-box"></i>' !!}</td>
                                    <td><b class="text-success">{{ $outlet->getTimeIn() }}</b></td>
                                    <td><b class="text-danger">{{ $outlet->getLatestTimeOut() }}</b></td>
                                    <td>
                                        <div class="outlets-action-btn-cntr"><a href="{{ route('outlet-dashboard', ['outlet_id' => $outlet->id()]) }}" class="btn btn-sm btn-primary">View</a></div>
                                        <?php if (!auth()->user()->is_read_only): ?>
                                        <div class="outlets-action-btn-cntr"><a href="{{ route('edit-outlet', ['outlet_id' => $outlet->id()]) }}" class="btn btn-sm btn-secondary">Edit</a></div>
                                        <!-- <div class="outlets-action-btn-cntr"><a href="{{ route('remove-outlet', ['outlet_id' => $outlet->id()]) }}" class="btn btn-sm btn-danger btn-secondary">Remove</a></div> -->
                                        <div class="page_switch_container" title="Disable or enable this outlet.">
                                          <div class="page__switch_raw">
                                            <div class="main-container">
                                              <div class="page__container">   
                                                <label class="switch switch_type1" role="switch">
                                                  <input type="checkbox" class="switch__toggle outlet_status_toggle" data-outlet-id="{{ $outlet->id() }}" {{ $outlet->getOutlet()->status == 'active' ? ' checked' : '' }}>
                                                  <span class="switch__label"></span>
                                                </label>  
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                    		<?php endif; ?>
                                        <!-- <a href="#" class="btn btn-sm btn-danger">Deactivate</a> -->
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="dataTables_info" id="e-commerce-orders-table_info" role="status" aria-live="polite">
                      <div id="pagi_page-content" class="pagi_page-content">Page {{ $page }} of {{$total_pages}}</div>
                </div>
                <div class="dataTables_paginate paging_simple_numbers" id="e-commerce-orders-table_paginate">
                    <a href="{{ route('all-outlets', ['page' => $prev]) }}" class="paginate_button previous" id="e-commerce-orders-table_previous">Previous</a> 
                    @for ($i = 1; $i <= $total_pages; $i++) 
                    <a href="{{ route('all-outlets', ['page' => $i]) }}" class="paginate_button current" aria-controls="e-commerce-orders-table" data-dt-idx="{{ $i }}" tabindex="0">{{ $i }}</a>
                    @endfor
                    <a href="{{ route('all-outlets', ['page' => $next]) }}" class="paginate_button next" aria-controls="e-commerce-orders-table" data-dt-idx="4" tabindex="0" id="e-commerce-orders-table_next">Next</a>                  
                </div>

            </div>
        </div>
    </div>
    <!-- / CONTENT -->
</div>

<script type="text/javascript" src="{{ url('/assets/js/apps/e-commerce/orders/orders.js?v=1')}}"></script>
<?php if (!auth()->user()->is_read_only): ?>
<script>
$(document).ready(function() {
	$('.outlet_status_toggle').change(function() {
		var checked = $(this).is(':checked');
		var outlet_id = $(this).data('outlet-id');

		$.ajax({
		    url: '{{ route('disable-outlet') }}',
		    type: 'post',
		    data: {'outlet_id': outlet_id, 'to_enable' : checked},
		    headers: {
		    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		    },
		    dataType: 'json',
		    cache: false,
		    success: function (r) {
			    if (typeof r.error == 'string') {
						alert(r.error);
				  }
		    },
		    error: function(e) {
		    	alert('Ticket has no winning bet.');
		    }
		});
	});
});
</script>
@endif
@endsection
