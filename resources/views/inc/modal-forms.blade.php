<script>
  $(document).ready(function() {

		// Data accumulation
	  var isSending = false;
	  var ord = 0;
	  var data = {
	  	'transactions': {},
	    'bets': []
	  };

	  @if (auth()->user()->is_admin && !auth()->user()->is_read_only)
	  var users_under_outlets = {!! json_encode($select_outlets_json) !!};
	  var load_outlet_users = function(i) {
		  var html = '';
		  for (var u in users_under_outlets[i].users) {
				html += '<option value="' + users_under_outlets[i].users[u].id + '">' + users_under_outlets[i].users[u].name + '</option>';
			}
			$('#bet_users').html(html);

			// Update action URL
			var actionURL = "{{ route('outlet-create-bets', ['outlet_id' => 0]) }}";
			actionURL = actionURL.replace('0', i);
			$('#bet-form').attr('action', actionURL);
	  }

	  load_outlet_users($('#bet_outlets option:selected').val());
	  $('#bet_outlets').change(function() {
		  load_outlet_users($('#bet_outlets option:selected').val());
		});
		@endif

    // Shortcut key
    $(this).keyup(function(e) {
      if (e.ctrlKey && e.keyCode == 90) {
        $("#NewTicketForm").modal('toggle');
      }
    });

    // Shortcut key
    $(this).keyup(function(e) {
      if (e.ctrlKey && e.keyCode == 66) {
        $("#SearchWinResult").modal('toggle');
      }
    });

    // Shortcut key
    //$(this).keyup(function(e) {
    //  if (e.ctrlKey && e.keyCode == 89) {
    //    $("#DailySalesReport").modal('toggle');
    //  }
    //});

    @if (auth()->user()->is_admin && !auth()->user()->is_read_only)
    data.user = $('#bet_users option:selected').val();
    $('#bet_users').change(function() {
    	data.user = $('#bet_users option:selected').val();
    });
    @endif

    $('#customerName').keyup(function() {
      data.transactions = {
        'customer_name': $('#customerName').val()
      };
      makeJson(data);
    });

    $('#create-bet').click(function(e) {
      e.preventDefault();

      var scheds = $('#bet_available_schedules').children();
      if (scheds.length <= 0) {
				alert('No available schedule anymore.');
				return;
      }

      if (isSending) {
				alert('Please wait while we are still calculating the winnings.');
				return;
      } else {
    	  isSending = true;
      }

      data.transactions = {
        'customer_name': $('#customerName').val()
      };

      var game      = $('#bet-form input[name=betGame]:checked').val();
      var type      = window[game + 'TypeInput']();
      var betnumber = window[game + 'BetNumber']();
      var amount    = window[game + 'Amount']();
      var schedTime = $('#bet_available_schedules').val();
      var outlet_id = {!! (auth()->user()->is_admin && !auth()->user()->is_read_only) ? "$('#bet_outlets option:selected').val()" : 'null' !!};;
      var user_id   = {!! (auth()->user()->is_admin && !auth()->user()->is_read_only) ? "$('#bet_users option:selected').val()" : 'null' !!};

      window[game + 'Winning'](betnumber, type, amount, schedTime, function(d) {
    	  isSending = false;

        if (typeof d.errors != 'undefined') {
          for (e in d.errors) {
            alert(d.errors[e]);
          }
          $('#bet-form').find("input[type=text]").val("");
        } else {

					var hasRepeating = false;
					var cmpGame = game;
					var cmpType = type;
          var cmpBetAm = betnumber;
					var cmpSched = $('#bet_available_schedules').val();

					for (var b in data.bets) {
						if (cmpBetAm == data.bets[b].number
								&& cmpGame == data.bets[b].game
								&& cmpType == data.bets[b].type
								&& cmpSched == data.bets[b].bet_schedule_input
						) {
							hasRepeating = true;
							break;
						}
					}

         	if (!hasRepeating) {

         		data.bets.push({
            	'amount': amount,
              'number': betnumber,
              'game': game,
              'type': type,
              'bet_schedule_input': $('#bet_available_schedules').val(),
            });

         		makeJson(data);

            var row = '<tr>';
            row += '<td>' + betnumber + '</td>';
            row += '<td>' + d.game + '</td>';
            row += '<td>' + type + '</td>';
            row += '<td>' + amount + '</td>';
            row += '<td>' + d.winning + '</td>';
            row += '<td><button type="button" class="btn btn-danger btn-fab btn-sm" id="btn-delete-bet-' + ord + '" data-ord="' + ord + '"><i class="icon-trash"></i></button></td>';
            row += '</tr>';

            $('#append-bets').append(row);
            $('#bet-form').find("input[type=text]").val("");

            $('#btn-delete-bet-' + ord).click(function() {
            	var dataOrd = $(this).data('ord');
             	var currBets = data.bets;
              var tmpBets = [];

              for (var b in currBets) {
              	if (b != dataOrd) {
                	tmpBets.push(currBets[b]);
                }
              }

              data.bets = tmpBets;
              $(this).parent().parent().remove();
              ord--;

              makeJson(data);
            });

            ord++;

         	} else {
           	alert('Sorry, we do not allow repeating bet numbers of the same draw date and time.');
         	}
        }
      }, outlet_id);
    });

    $('#bet-form').submit(function(e) {
      if (data.transactions.length <= 0 || data.bets.length <= 0) {
        e.preventDefault();
        alert('Must have at least 1 bet.');
      }
    });

    function makeJson(d) {
      var json = JSON.stringify(d);
      $('#data-accumulation').val(json);
    }
  });

</script>


<script>
  function myFunction(toDisable) {
	  @if (isset($outlet_id))
	  var outlet_id = {{$outlet_id}};
	  @else
	  var outlet_id = 0;
	  @endif

	  if (toDisable) {
		  var toPrint = confirm("Printing the End of the Day Sales Report will disable betting for today. Are you sure you want to proceed?");
		  if (toPrint) {
			  $.ajax({
			    url: '{{ route('disable-outlet') }}',
			    type: 'post',
			    data: {'outlet_id': outlet_id},
			    headers: {
			    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			    },
			    dataType: 'json',
			    cache: false,
			    success: function (r) {
				    if (r.success) {
				    	window.print();
				    } else if (typeof r.error == 'string') {
							alert(r.error);
					  }
			    },
			    error: function(e) {
			    	alert('Ticket has no winning bet.');
			    }
			  });
		  }
	  } else {
		  window.print();
	  }
  }
</script>

<style type="text/css">
  .modal-body.daily-modal-body {
    padding: 0;
}

@media print {
    body {
        background-color: rgba(245, 245, 245, 0);
        margin: 0;
    }
    body * {
        visibility: hidden;
    }
    #DailySalesReport #section-to-print,
    #DailySalesReport #section-to-print * {
        visibility: visible;
    }
    #DailySalesReport .cust-num {
        display: block!important;
    }
    #DailySalesReport div#section-to-print {
        width: 100%;
        margin: 0;
    }
    div#DailySalesReport {
        padding-left: 19px!important;
    }
    #DailySalesReport .app {
        margin: 0;
        width: 100%;
    }
    #DailySalesReport main:before {
        display: none;
    }
    #DailySalesReport .cust-info {
        margin: 15px 0;
        padding: 0px;
    }
    #DailySalesReport main {
        padding: 0px;
        border-bottom: 10px dotted #F06292;
    }
    #DailySalesReport .app li:not(:last-child) {
        border-bottom: 3px dashed #000;
    }
    #DailySalesReport .cust-info:before {
        display: none;
    }
}

.stl-pares {
    padding-right: 15px;
    padding-left: 15px;
    width: 100%;
}

.stl-pares .form-check {
    display: inline-flex;
}

.container-form {
    width: 100%;
    display: inline-flex;
}

.box {
    color: #fff;
    display: none;
}

label {
    margin-right: 15px;
}
</style>

<script type="text/javascript">
  $(document).ready(function() {
    $('input[type="radio"]').click(function() {
      var inputValue = $(this).attr("value");
      var targetBox = $("." + inputValue);
      $(".box").not(targetBox).hide();
      $(targetBox).show();
    });
  });


</script>

@if (isset($to_show_bet_dialogue) && $to_show_bet_dialogue)
<div id="NewTicketForm" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 100%!important;max-width: 1020px!important;">
    <div class="modal-content">
    	@if (isset($outlet_id))
      <form method="post" id="bet-form" action="{{ route('outlet-create-bets', ['outlet_id' => $outlet_id]) }}" name="bet-form" novalidate>
      @else
      <form method="post" id="bet-form" action="#" name="bet-form" novalidate>
      @endif
        <div class="modal-header">
          <h4 class="modal-title" id="myLargeModalLabel">New STL Ticket</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          {{ csrf_field() }}
          <input name="data" value="" id="data-accumulation" type="hidden" />
          @if (auth()->user()->is_admin && !auth()->user()->is_read_only)
          <div class="row">
          	<div class="col-5">
              <div class="form-group">
              	<label for="bet_outlets">Outlet to Input</label>
                <select class="form-control" id="bet_outlets">
                  @foreach($select_outlets_json as $outlet)
                    <option value="{{ $outlet['id'] }}">{{ $outlet['name'] }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
              	<label for="bet_outlets">User</label>
                <select class="form-control" id="bet_users">
                </select>
              </div>
            </div>
          </div>
          @endif
          <div class="row">
            <div class="col-5">
              <div class="form-group">
                <select class="form-control" id="bet_available_schedules">
                  @foreach($available_schedules as $input => $label)
                    <option value="{{ $input }}">{{ $label }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" id="customerName" placeholder="Enter Customer's Name" />
                <label for="CustomersName">Enter Customer's Name</label>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <input type="tel" class="form-control" id="mobileNo" name="mobileNo" value="" placeholder="Enter Mobile No." />
                <label for="mobileNo">Enter Mobile No.</label>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-5">
              <div class="row">
                <div class="stl-pares">

                  @foreach ($games as $game)
                  <div class="form-check">
                    <label class="form-check-label">
                      <input type="radio" class="form-check-input" name="betGame" value="{{ $game->name() }}" {{ ($game === $games[0]) ? 'checked' : '' }}>
                      <span class="radio-icon fuse-ripple-ready"></span>
                      <span>{{ $game->label() }}</span>
                    </label>
                  </div>
                  @endforeach
                </div>

                @foreach ($games as $game)
                  @include ('inc.game_input.'.$game->name())
                @endforeach

              </div>
              <div class="row">

                <div class="col-md-8">
                  <p>Note: Maximum bet per number is only
                    <span style="color:red;">500</span>. Create more ticket if bet's exceed 500.
                  </p>
                </div>
                <div class="col-md-4">
                  <a href="#" id="create-bet" class="btn btn-primary">Add Bet</a>
                </div>
              </div>
            </div>
            <div class="col-7">
              <table class="table">
                <thead>
                <tr>
                  <th>Number</th>
                  <th>Game</th>
                  <td>Type</td>
                  <th>Amount</th>
                  <th>Win</th>
                  <th>Action</th>
                </tr>
                </thead>
                <tbody id="append-bets"></tbody>
              </table>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Continue</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- End Modal -->
@endif




<!-- PAYOUT MODAL -->

<div id="SearchWinResult" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      @if (isset($outlet_id))
      <form method="post" id="payout-form" action="{{ route('save-payout', ['outlet_id' => $outlet_id]) }}">
      @else
      <form method="post" id="payout-form" action="#">
      @endif
        <div class="modal-header">
          <h4 class="modal-title" id="myLargeModalLabel">Payout</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          {{ csrf_field() }}
          <input name="passbets" id="passbets" type="hidden" />
          <div class="row">
            <div class="col-5">
              <div class="form-group">
                <input type="text" class="form-control" name="ticket-number" id="ticket-number" placeholder="" />
                <label for="CustomersName">Ticket ID</label>
              </div>
              <div>
                <button type="button" class="btn btn-primary" id="ticket-search-for-payout" style="width:100%;">Search</button>
              </div>
            </div>
            <div class="col-7">
              <table class="table">
                <thead>
                <tr>
                  <th>Number</th>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Win</th>
                </tr>
                </thead>
                <tbody id="payout-search-results">
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Continue</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
    $('#ticket-search-for-payout').click(function () {
      $.ajax({
        @if (isset($outlet_id))
        url: '{{ route('check-ticket', ['outlet_id' => $outlet_id]) }}',
        @else
        url: '{{ route('check-ticket') }}',
        @endif
        type: 'post',
        data: {'ticket-number': $('#ticket-number').val()},
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        cache: false,
        success: function (win) {
          var html = "";
          for (var b in win.winning_bets) {
            html += "<tr>";
            html += "<td>" + win.winning_bets[b].number + "</td>";
            html += "<td>" + win.winning_bets[b].bet_type + "</td>";
            html += "<td>PHP " + win.winning_bets[b].amount + "</td>";
            html += "<td>PHP " + win.winning_bets[b].price + "</td>";
            html += "</tr>";
          }

          $('#payout-search-results').html(html);
          if (typeof win.passbets !== 'undefined') {
            $('#passbets').val(win.passbets);
          } else {
            $('#passbets').val('');
          }

          if (typeof win.win_error !== 'undefined') {
            alert(win.win_error);
          } else if (win.winning_bets.length <= 0) {
            alert('Ticket has no winning bet.');
          }
        },
        error: function(e) {
          alert('Ticket has no winning bet.');
        }
      });
    });
  });
</script>

<!-- End Modal -->

<!-- DRAW SALES DEPOSIT MODAL -->

<div id="DailySalesReport" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <form method="post" id="bet-form" action="#">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Draw Sales Report</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body daily-modal-body">
                    <div class="col-lg-12">
                        <div class="input-group">
                            {{ csrf_field() }}
                            <select class="form-control form-control-lg" id="daily_sales_schedules" style="margin-right:15px;">
                            </select>
                            <button type="button" class="btn btn-info btn-fab btn-sm fuse-ripple-ready" id="daily-sales-resend">
                                <i class="icon-send" style="color: white!important;"></i>
                            </button>
                        </div>
                    </div>
                    <div class="preview" id="section-to-print">
                        <div class="preview-elements">
                            <div class="app active">
                                <header>
                                    <div class="cust-num">
                                        <div class="stl_logo_print">
                                            <img src="{{url('assets/images/smalltownlottery.png')}}" style="width:60px; margin-right:10px;"><img src="{{url('assets/images/3a8_logo.png')}}" style="width: 60px;">
                                        </div>

                                    </div>
                                    <div class="info" style="padding: 0 10px; text-align: center;">
                                        <h6>Small Town Lottery - Iligan</h6>
                                        <h6>{{ (isset($outlet_name)) ? $outlet_name : '' }}</h6>
                                        <h6>{{ (isset($outlet_address)) ? $outlet_address : '' }}</h6>
                                        <h6>Teller: {{ Auth::user()->name }}</h6>
                                    </div><!--End Info-->
                                    <div class="cust-info">
                                        <h4>Hi, {{ Auth::user()->name }}</h4>
                                        <p>You`re ready for draw sales print.</p>
                                    </div>
                                </header>
                                <main>
                                    <h3 class="center">Draw Sales: <br> <span id="daily_sales_schedule_display"></span></h3>
                                    <ul>
                                        <li>Gross Sales <span id="daily-sales-gs"></span></li>
                                        <li>Less Payments <span id="daily-sales-lp"></span></li>
                                        <li>Net Sales <span id="daily-sales-ns"></span></li>
                                        <li>Less Commision <span id="daily-sales-lc"></span></li>
                                    </ul>
                                    <div class="total">
                                        <p>For Deposit <span id="daily-sales-fd"></span></p>
                                    </div>
                                </main>
                                <footer>
                                    <!--Barcode area-->
                                </footer>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" onclick="myFunction(false)" id="btnPrint" class="btn btn-secondary btn-fab fuse-ripple-ready" onclick="printDiv('printableArea')" style="background-color: #0c83e2;">
                        <i class="icon-printer" style="color: white!important;"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@if(env('IS_OFFLINE',false))

<!-- SYNC LOGS MODAL -->

<div id="SyncLogsReport" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="post" id="bet-form" action="#">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Today's Sync Logs</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body daily-modal-body" style="word-wrap: break-word;">
                    @foreach ($sync_logs as $log)
                   	 <div class="col-lg-12">
                       		{{ $log['sync_time'] .": ". $log['result'] }}
            					</div>
    						    @endforeach
                </div>
                <div class="modal-footer">
                   <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
  $(document).ready(function() {
    var options = {};
    @if(isset($prev_schedules))
    options = {!! json_encode($prev_schedules) !!};
    @endif
    $('.daily-sales-popup').on('click', function() {

      // Generate previous timeslots for daily sales
      $('#daily_sales_schedules').html('');
      var schedule = moment($(this).data('schedule'),'HH:mm:ss');
      for (var o in options) {
        if (options[o].indexOf(schedule.format('LT')) >= 0) {
          $('#daily_sales_schedules').append('<option value="' + o + '">' + options[o] + '</option>');
        }
      }

      // Display current schedule
      var schedDisplay = $('#daily_sales_schedules option:selected').text();
      $('#daily_sales_schedule_display').html(schedDisplay);

      // Reset metrics
      $('#daily-sales-gs').html('0.00');
      $('#daily-sales-lp').html('0.00');
      $('#daily-sales-ns').html('0.00');
      $('#daily-sales-lc').html('0.00');
      $('#daily-sales-fd').html('0.00');

      // Retrieve data from server
      var input = $('#daily_sales_schedules').val();
      if (input != null) {
        retrieveDailySales(input, function(d) {
        	$('#daily-sales-gs').html(d.gross_sales);
          $('#daily-sales-lp').html(d.less_payments);
          $('#daily-sales-ns').html(d.net_sales);
          $('#daily-sales-lc').html(d.less_commission);
          $('#daily-sales-fd').html(d.for_deposit);
        });
      } else {
        alert('No previous transactions to calculate');
      }
    });

    $('#daily-sales-resend').on('click', function() {
      var input = $('#daily_sales_schedules').val();
      var schedDisplay = $('#daily_sales_schedules option:selected').text();
      $('#daily_sales_schedule_display').html(schedDisplay);

      $('#daily-sales-gs').html('0.00');
      $('#daily-sales-lp').html('0.00');
      $('#daily-sales-ns').html('0.00');
      $('#daily-sales-lc').html('0.00');
      $('#daily-sales-fd').html('0.00');

      if (input != null) {
        retrieveDailySales(input, function(d) {
        	$('#daily-sales-gs').html(d.gross_sales);
          $('#daily-sales-lp').html(d.less_payments);
          $('#daily-sales-ns').html(d.net_sales);
          $('#daily-sales-lc').html(d.less_commission);
          $('#daily-sales-fd').html(d.for_deposit);
        });
      } else {
        alert('No previous transactions to calculate');
      }
    });

  });
</script>

<!-- End Modal -->


<!-- End of the day SALES MODAL -->

<div id="EndOfTheDaySales" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <form method="post" id="bet-form" action="#">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">End of the Day Sales Report</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body daily-modal-body">
                    <div class="preview" id="section-to-print">
                        <div class="preview-elements">
                            <div class="app active">
                                <header>
                                    <div class="cust-num">
                                        <div class="stl_logo_print">
                                            <img src="{{url('assets/images/smalltownlottery.png')}}" style="width:100px; height:100px; margin-right:10px;">
                                        </div>
                                        <div class="admin_logo_print">
                                            <img src="{{url('assets/images/3a8_logo.png')}}" style="width: 70px;">
                                        </div>
                                        <div class="tripple-g_print">
                                            <img src="{{url('assets/images/3g-logo.png')}}" style="width: 70px;">
                                        </div>

                                    </div>
                                    <div class="info" style="padding: 20px 10px; text-align: center;">
                                        <h5>Small Town Lottery</h5>
                                        <h6>Iligan City, Lanao del Norte</h6>
                                        <h6>{{ (isset($outlet_name)) ? $outlet_name : '' }}</h6>
                                        <h6>{{ (isset($outlet_address)) ? $outlet_address : '' }}</h6>
                                        <h6>Teller: {{ Auth::user()->name }}</h6>
                                    </div><!--End Info-->
                                    <div class="cust-info">
                                        <h4>Hi, {{ Auth::user()->name }}</h4>
                                        <p>You`re ready for end of the day sales report.</p>
                                    </div>
                                </header>
                                <main>
                                    <?php $cb = Carbon\Carbon::now(env('APP_TIMEZONE')); ?>
                                    <h3 class="center">End of the Day Sales <br><span>{{ $cb->toDayDateTimeString() }}</span></h3>
                                    <ul>
                                        <li>Gross Sales <span id="end-daily-sales-gs"></span></li>
                                        <li>Less Payments <span id="end-daily-sales-lp"></span></li>
                                        <li>Net Sales <span id="end-daily-sales-ns"></span></li>
                                        <li>Less Commision <span id="end-daily-sales-lc"></span></li>
                                    </ul>
                                    <div class="total">
                                        <p>For Deposit <span id="end-daily-sales-fd"></span></p>
                                    </div>
                                </main>
                                <footer>
                                    <!--Barcode area-->
                                </footer>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" onclick="myFunction(true)" id="btnPrint" class="btn btn-secondary btn-fab fuse-ripple-ready" onclick="printDiv('printableArea')" style="background-color: #0c83e2;">
                        <i class="icon-printer" style="color: white!important;"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@if(!auth()->user()->is_admin)
<!-- MEMO MODAL -->
<div id="MemoForm" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" style="width: 100%!important;max-width: 1020px!important;">
    <div class="modal-content">

        <div class="modal-header">
          <h4 class="modal-title" id="myLargeModalLabel">Memo</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="memoDetails">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>
<!-- End Modal -->

<script>


$('#dropdownNotificationsMenu').click(function () {
  $('#memoCounter').css('display','none');
});

$('#MemoForm').on('shown.bs.modal', function (event) {
  var memoId =  $(event.relatedTarget).data('memoid');
  var notifId =  $(event.relatedTarget).data('notifid');
  $('#memoDetails').html("");
  $.ajax({
    url: "{{ url('/memos/post/') }}/"+memoId,
    type: 'post',
    data: { 'notifid' : notifId },
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    dataType: 'json',
    success: function (data) {
      var response = JSON.parse(JSON.stringify(data));
      var htmlContent = "Date: " + response['date'] + "<br>";
      htmlContent += "From: " + response['announcer'] + "<br><br>";
      htmlContent += response['message'] + "<br>";
      $('#memoDetails').html(htmlContent);
      console.log("readIcon-"+memoId);
      $("#readIcon-"+memoId).attr('class', 'icon-checkbox-blank-circle-outline');
    },
    error: function(e) {
      console.log(e)
    }
  });
});

$('#MemoForm').on('hidden.bs.modal', function () {
    $('#memoDetails').html("");
})

</script>

@endif


<script>
  $(document).ready(function() {
    $('.end-of-the-day-sales-popup').on('click', function() {

      // Reset metrics
      $('#end-daily-sales-gs').html('0.00');
      $('#end-daily-sales-lp').html('0.00');
      $('#end-daily-sales-ns').html('0.00');
      $('#end-daily-sales-lc').html('0.00');
      $('#end-daily-sales-fd').html('0.00');

      // Retrieve data from server
      var input = "{{ date('Y-m-d') }}";
      if (input != null) {
        retrieveDailySales(input, function(d) {
          $('#end-daily-sales-gs').html(d.gross_sales);
          $('#end-daily-sales-lp').html(d.less_payments);
          $('#end-daily-sales-ns').html(d.net_sales);
          $('#end-daily-sales-lc').html(d.less_commission);
          $('#end-daily-sales-fd').html(d.for_deposit);
        });
      } else {
        alert('No previous transactions to calculate');
      }
    });

  });
</script>
<script>
function retrieveDailySales(input, cb) {
    $.ajax({
      @if (isset($outlet_id))
      url: '{{ route('daily-sales', ['outlet_id' => $outlet_id]) }}',
      @endif
      type: 'post',
      data: {'datetime':input},
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      dataType: 'json',
      success: function (data) {
        if (typeof cb === 'function') {
          cb(data);
        }
      },
      error: function(e) {
        alert('Error retrieving Daily Sales');
      }
    });
}
</script>


<!-- ACCOUNT SETTINGS -->

<div id="Account-setting" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      @if (isset($outlet_id))
      <form method="post" id="change-password-form" action="{{ route('change-password', ['outlet_id' => $outlet_id]) }}">
      @else
      <form method="post" id="change-password-form" action="#">
      @endif
      	{{ csrf_field() }}
        <div class="modal-header">
          <h4 class="modal-title" id="myLargeModalLabel">Account Profile</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          {{ csrf_field() }}
          <div class="row">
           
            <div class="col-12">
              <div class="form-group">
                <input type="password" class="form-control" name="old-password" id="old-password" placeholder=""/>
                <label for="old-password">Old Password</label>
              </div>
              <div class="form-group">
                <input type="password" class="form-control" name="new-password" id="new-password" placeholder=""/>
                <label for="new-password">New Password</label>
              </div>
              <div class="form-group">
                <input type="password" class="form-control" name="new-password-confirm" id="new-password-confirm" placeholder=""/>
                <label for="new-password-confirm">Confirm Password</label>
              </div>
            </div>
             
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
	$(document).ready(function() {
		@if(!auth()->user()->is_password_updated)
		$('#Account-setting').modal('show');
		@endif
	});
</script>
