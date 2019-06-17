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
                    		@if (date('Y-m-d') === $draw_date)
                        <div class="h4">Hot Numbers as of <b>today</b>.</div>
                        @else
                        <div class="h4">Hot Numbers as of <b>{{ date('l, F j, Y', strtotime($draw_date)) }}</b>.</div>
                        @endif
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
                
                		<div class="col-sm-2" style="margin-top: 10px;">
                			<select id="hotnumbers-game" class="form-control">
                				<option value="{{ \App\System\Games\Swertres\SwertresGame::name() }}"{{ $game == \App\System\Games\Swertres\SwertresGame::name() ? ' selected' : '' }}>{{ \App\System\Games\Swertres\SwertresGame::GAME_LABEL }}</option>
                				<option value="{{ \App\System\Games\SwertresSTL\SwertresSTLGame::name() }}"{{ $game == \App\System\Games\SwertresSTL\SwertresSTLGame::name() ? ' selected' : '' }}>{{ \App\System\Games\SwertresSTL\SwertresSTLGame::GAME_LABEL }}</option>
                			</select>
                			<script>
                            $(document).ready(function() {
                                $('#hotnumbers-game').change(function() {
                                    var url = "{{ route('reports-hotnumbers') }}/{{ $top }}/" + $(this).val();
                                    window.location.href = url;
                                });
                            });
                            </script>
                		</div>

                    <div class="col-sm-2" style="margin-top: 10px;">
                        <input type="text" id="time-machine-datepicker" class="h6 custom-select form-control" placeholder="Pick a Date" value="{{ $draw_date }}">
                        <script>
                            $(document).ready(function() {
                                $('#time-machine-datepicker').datepicker({format:'yyyy-mm-dd'});
                                $('#time-machine-datepicker').change(function() {
                                    var url = "{{ route('reports-hotnumbers', ['top' => $top, 'game' => $game]) }}/" + $(this).val();
                                    window.location.href = url;
                                });
                            });
                            </script>
                    </div>
                    <div class="col-sm-2" style="margin-top: 10px;">
                        <input type="text" id="hotnumbers-top-choice" class="form-control" placeholder="Top" value="{{$top}}">
                        <script>
                        $(document).ready(function() {
                            	$("#hotnumbers-top-choice").on('keyup', function (e) {
                            	    if (e.keyCode == 13) {
                                	  if ($(this).val().length > 0) {
                                		  var url = "{{ route('reports-hotnumbers') }}/" + $(this).val() + "/{{ $game }}/{{ $draw_date }}";
                              	    	window.location.href = url;
                                	  } else {
                                    	  alert("Please provide an integer as input.");
                            	    	}
                            			}
                            	});
                        });
                        </script>
                    </div>
                    
                    <div class="col-sm-2" style="margin-top: 10px;">
                        <select id="hotnumbers-sched-key" class="form-control">
                        	<option value="">All Draw Schedules</option>
                        	@foreach (\App\System\Data\Timeslot::drawTimeslots() as $key => $time)
                        	<option value="{{ urlencode($key) }}"{{($key == $sched_key) ? ' selected' : ''}}>{{ date('g A', strtotime($time)) }}</option>
                        	@endforeach
                        </select>
                        <script>
                        $(document).ready(function() {
                            $('#hotnumbers-sched-key').change(function() {
                                var url = "{{ route('reports-hotnumbers', ['top' => $top, 'game' => $game, 'draw_date' => $draw_date]) }}/" + $(this).val();
                                window.location.href = url;
                            });
                        });
                        </script>
                    </div>
                    
                    <div class="col-sm-2" style="margin-top: 10px;">
                    		<input type="text" id="hotnumbers-search" class="form-control" placeholder="Search bet">
                    		<script>
                        $(document).ready(function() {
                            	$("#hotnumbers-search").on('keyup', function (e) {
                                var date = $('#time-machine-datepicker').val();
                                var schedKey = $('#hotnumbers-sched-key').val();
                                var search = $(this).val();
																var json = filter_json(search, hotnumbers);
																render_table($('#hotnumbers-results'), json);
																if (typeof search == "string" && search.length >= 3) {
																	$.ajax({
																	    url: '{{ route('reports-hotnumbers-capture') }}',
																	    type: 'post',
																	    data: {
																				'result_date' : date,
																				'schedule_key' : schedKey,
																				'keyword' : search
																			},
																	    headers: {
																	    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
																	    },
																	    dataType: 'json',
																	    cache: false,
																	    success: function (r) {
																		    // do nothing
																	    },
																	    error: function(e) {
																	    	// do nothing
																	    }
																	});
																}
                            	});
                        });
                        </script>
                    </div>

                </div>  

            </div>
            <!-- / CONTENT TOOLBAR -->

            <div id="e-commerce-orders-table_wrapper" class="dataTables_wrapper no-footer">
               <div class="dataTables_scroll">
                  <div class="dataTables_scrollBody">
                    <table id="e-commerce-orders-table" class="table table-hover dataTable">
                      <thead>
                          <tr>
                          	<th>Bet Number</th>
                            <th>Total Amount</th>
                            <th>Bet Count</th>
                          </tr>
                      </thead>
                      <tbody id="hotnumbers-results">
                      </tbody>
                    </table>
                  </div>
                </div>
            </div>

        </div>
    </div>
    <!-- / CONTENT -->
</div>

<script>
    var hotnumbers = {!! $hot_numbers_json !!};
    $(document).ready(function() {
    	render_table($('#hotnumbers-results'), hotnumbers);
    });

    function filter_json(keyword, json) {
			var resJson = [];
			for (var h in json) {
				var bet = json[h].number;
				if (bet.includes(keyword)) {
					resJson.push(json[h]);
				}
			}

			return resJson;
    }

    function render_table(obj, json) {
      obj.html('');
    	for (var h in json) {
    			var html = '<tr>';
    			html += '<td><span>' + json[h].number + '</span></td>';
    			html += '<td><span>PHP ' + number_format(json[h].total_amount, 2, '.', ',') + '</span></td>';
    			html += '<td><b><span>' + json[h].bet_count + '</span></b></td>';
    			html += '</tr>';
    
    			obj.append(html);
      }
    }
    
    function number_format (number, decimals, dec_point, thousands_sep) {
        // Strip all characters but numerical ones.
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }
</script>

@endsection
