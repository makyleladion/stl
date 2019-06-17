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
                        <div class="h4">Users</div>
                        <div class="">Total Users: {{ $total_users }}</div>
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
                <a href="{{route('new-user')}}" class="btn btn-secondary">ADD NEW USER</a>
            </div>

        </div>
        <!-- / HEADER -->

        <div class="page-content-card">
            <div id="e-commerce-orders-table_wrapper" class="dataTables_wrapper no-footer">

                <div class="dataTables_scroll">

                    <div class="dataTables_scrollBody">

                        <table id="e-commerce-products-table" class="table dataTable">

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
                                            <span class="column-title">Email</span>
                                        </div>
                                    </th>

                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Role</span>
                                        </div>
                                    </th>

                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Action</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">
                                                  Betting
                                            </span>
                                        </div>
                                    </th>

                                </tr>
                            </thead>

                            <tbody>

                                @foreach ($users as $user)
                                <tr>
                                    <td>{{ $user->id(true) }}</td>
                                    <td>{{ $user->name() }}</td>
                                    <td>{{ $user->email() }}</td>
                                    <td>{{ ucfirst($user->role()) }}</td>
                                    <td>
                                    	@if (!auth()->user()->is_read_only)
                                        <a href="{{ route('edit-user', ['user_id' => $user->id()]) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        @if (auth()->user()->id != $user->id())
                                        <a href="{{ route('delete-user', ['user_id' => $user->id()]) }}" class="btn btn-danger btn-sm delete-btn">Delete</a>
                                        @endif
                                        @endif
                                        <a href="{{ route('view-user-log', ['user_id' => $user->id()]) }}" class="btn btn-sm btn-default">Logs</a>
                                    </td>
                                    <td>
                                        @if (!$user->isAdmin() && !$user->isSuperAdmin())
                                        <div class="page_switch_container" title="Disable or enable betting for this user.">
                                          <div class="page__switch_raw">
                                            <div class="main-container">
                                              <div class="page__container">   
                                                <label class="switch switch_type1" role="switch">
                                                  <input type="checkbox" class="switch__toggle user_betting_status_toggle" data-user-id="{{ $user->id() }}" {{ $user->isBettingEnabled() == 1 ? ' checked' : '' }}>
                                                  <span class="switch__label"></span>
                                                </label>  
                                              </div>
                                            </div>
                                          </div>
                                        </div>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="dataTables_info" id="e-commerce-orders-table_info" role="status" aria-live="polite">
                  <div id="pagi_page-content" class="pagi_page-content">Page {{ $page }} of {{ $total_pages }}</div>
                </div>
                <div class="dataTables_paginate paging_simple_numbers" id="e-commerce-orders-table_paginate">
                   <a href="{{ route('all-users', ['page' => $prev]) }}" class="paginate_button previous" id="e-commerce-orders-table_previous">Previous</a> 
                    @for ($i = 1; $i <= $total_pages; $i++) 
                    <a href="{{ route('all-users', ['page' => $i]) }}" class="paginate_button current" aria-controls="e-commerce-orders-table" data-dt-idx="{{ $i }}" tabindex="0">{{ $i }}</a>
                    @endfor
                    <a href="{{ route('all-users', ['page' => $next]) }}" class="paginate_button next" aria-controls="e-commerce-orders-table" data-dt-idx="4" tabindex="0" id="e-commerce-orders-table_next">Next</a>
                </div>
            </div>
        </div>
    </div>
    <!-- / CONTENT -->
</div>

<script type="text/javascript" src="{{ url('/assets/js/apps/e-commerce/orders/orders.js?v=1')}}"></script>
<script>
$(document).ready(function() {
	$(".delete-btn").click(function(){
		return confirm("Are you sure you want to delete this user?");
    });
	$('.user_betting_status_toggle').change(function() {
		var checked = $(this).is(':checked');
		var user_id = $(this).data('user-id');

		$.ajax({
		    url: '{{ route('disable-user-betting') }}',
		    type: 'post',
		    data: {'user_id': user_id, 'to_enable' : checked},
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
		    	alert('Cannot update the users betting status.');
		    }
		});
	});
});
</script>

@endsection
