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
                        <div class="h4">Winnings Calculation.</div>
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
                            });
                            </script>
                    </div>
                    
                    <div class="col-sm-3" style="margin-top: 10px;">
                        <input type="text" id="date-to-machine-datepicker" class="h6 custom-select form-control" placeholder="Pick a Date" value="{{$date_to}}">
                        <script>
                            $(document).ready(function() {
                                $('#date-to-machine-datepicker').datepicker({format:'yyyy-mm-dd'});
                            });
                            </script>
                    </div>

                    <div class="col-sm-6">
                      <button class="btn btn-secondary" id="calculate-btn"><i class="icon icon-magnify s-6"></i>Calculate</button>
                      <span style="margin-left: 10px; display: none;" id="wait-label"></span>
                    </div>

                </div>  

            </div>
            <!-- / CONTENT TOOLBAR -->

						<div class="row">
                <div class="col-12 col-sm-6 col-xl-12 p-4">
                    <div class="widget widget10 card" id="winnings-content" style="display: none;">
    
                        <div class="widget-header pl-4 pr-2 row no-gutters align-items-center justify-content-between">
    
                            <div class="col">
                                <span class="h5">Total Payouts (PHP <span id="overall_total">0</span>) - <span id="date_text"></span></span>
                            </div>
    
                            <button type="button" class="btn btn-icon fuse-ripple-ready">
                                <i class="icon icon-dots-vertical"></i>
                            </button>
    
                        </div>
    
                        <div class="widget-content p-4">
                
                            <table class="table table-responsive table-striped">
                              <thead>
                                  <tr>
                                  @foreach (\App\System\Games\GamesFactory::getGames() as $game)
                                    <th>{{ $game->label() }}</th>
                                  @endforeach
                                  </tr>
                              </thead>
                              <tbody id="hotnumbers-results">
                                  <tr>
                                  @foreach (\App\System\Games\GamesFactory::getGames() as $game)
                                      <td>PHP <span id="game_total_{{ $game::name() }}">0</span></td>
                                  @endforeach
                                  </tr>
                              </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
    <!-- / CONTENT -->
</div>

<script type="text/javascript" src="https://js.pusher.com/4.3/pusher.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {

	// Button event
	$('#calculate-btn').click(function() {
				$('#wait-label').show();
				$('#wait-label').html('Trigger calculation...');
		
				var dateFrom = $('#date-from-machine-datepicker').val();
        var dateTo = $('#date-to-machine-datepicker').val();

        var dateFromTimestamp = new Date(dateFrom).getTime();
        var dateToTimestamp = new Date(dateTo).getTime();

        if (dateFromTimestamp > dateToTimestamp) {
            alert('Input error. "From" must be lower than "To".');
        } else {
						var url = "{{ route('reports-date-range-calculations-winnings-trigger') }}/" + dateFrom + "/" + dateTo;
						$.get( url , function( data ) {
							  if (data.success) {
								  $('#wait-label').html('Please wait while we calculate the data...');
							  } else {
									alert(data.message);
									console.log(data);
								}
						}, "json" );
        }
	});

	// Listen to pusher
	var app_key = "{{ env('PUSHER_APP_KEY') }}";
	var app_cluster = "{{ env('PUSHER_APP_CLUSTER') }}";
	
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

	var channel = pusher.subscribe('private-winnings-broadcast');
	channel.bind('winnings-broadcast.calculated', function(data) {
		$('#wait-label').hide();
		$('#winnings-content').show();
		$('#overall_total').html(numberWithCommas(data.total));
		$('#date_text').html(data.date_range_text);

		for (var d in data.total_per_game) {
			var sel = '#game_total_' + d;
			$(sel).html(numberWithCommas(data.total_per_game[d]));
		}
	});

	const numberWithCommas = (x) => {
		var parts = x.toString().split(".");
		parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
		return parts.join(".");
	}
});
</script>

@endsection
