@extends('layouts.main') @section('content')

                    <div class="row">
                        <div class="col-md-12 mt-1 mb-1">
                            <div class="form-actions clearfix">
                                <div class="float-left">
                                    <div class="content-header">Outlets Page</div>
                                    <p class="content-sub-header">Total Outlets: {{ $total_outlets }}</p>
                                </div>
                                <div class="float-right">
                                    <div class="my-4 pr-3">
                                    <a href="{{route('new-outlet')}}" class="py-1 h6""><i class="icon-home font-medium-5 mr-2"></i>New Outlet</a></div>
                                </div>
                            </div>
                        </div>
                    </div>                    
                    <section id="extended">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="form-actions clearfix">
                                            <div class="float-left"><h4 class="card-title">List of Outlets</h4></div>
                                            <div class="float-right"><input type="text" class="form-control" id="basicInput" placeholder="Search by Outlet Name"></div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <table class="table table-responsive-lg text-left">
                                                <thead>
                                                    <tr>
                                                        <th class="th-width">Name</th>
                                                        <th class="th-width">Address</th>
                                                        <th class="th-width">Owner</th>
                                                        <th class="th-width">Teller</th>
                                                        <th>Time-in</th>
                                                        <th>Time-out</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($outlets as $outlet)
                                                    <tr>
                                                        <td>{{ $outlet->name() }}</td>
                                                        <td>{{ $outlet->address() }}</td>
                                                        <td>{{ $outlet->owner() }}</td>
                                                        <td>{{ $outlet->assignedTellers(true) }}</td>
                                                        <td><b class="text-success">{{ $outlet->getTimeIn() }}</b></td>
                                                        <td><b class="text-danger">{{ $outlet->getLatestTimeOut() }}</b></td>
                                                        <td>
                                                        <?php if (!auth()->user()->is_read_only): ?>                
                                                            <a href="{{ route('edit-outlet', ['outlet_id' => $outlet->id()]) }}" class="btn btn-flat btn-primary m-0 px-1" data-toggle="tooltip" data-placement="top" title="Edit Outlet" data-trigger="hover"> <i class="ft-edit font-medium-3"></i></a>
                                                            <a href="#" id="confirm-delete-outlet" class="btn btn-flat btn-danger m-0 px-1" data-toggle="tooltip" data-placement="top" title="Remove Outlet" data-trigger="hover"> <i class="ft-trash-2 font-medium-3"></i></a>
                                                            <input type="checkbox" data-toggle="tooltip" data-placement="top" title="Disable Outlet/Enable Outlet" data-trigger="hover" id="enable-disable" class="switchery outlet_status_toggle" data-size="sm" checked/>
                                                        <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>


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
