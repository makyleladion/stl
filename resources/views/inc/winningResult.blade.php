<!-- Winning Result -->
<div id="WinningResult" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <form method="post" id="winning-form" action="{{ route('set-winning-result') }}" name="bet-form" novalidate>
        <div class="modal-header">
          <h4 class="modal-title" id="myLargeModalLabel">Winning Result</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          {{ csrf_field() }}
          <input name="winning-timeslot" value="" id="selected-schedule" type="hidden" />
          <div class="row">
            <div class="form-group col-12">
              <h2 class="text-center">Draw time: <span id="drawtime-display"></span></h2>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
            @foreach (\App\System\Games\GamesFactory::getGameNames() as $gameName)
              @include('inc.winning_input.'.$gameName)
            @endforeach
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

<script>
  $(document).on("click", ".winning-result-modal", function () {
    var timeslot = $(this).data('schedule');
    var datetime = '{{date('Y-m-d')}} ' + timeslot;
    var time = moment(datetime);

    $('#selected-schedule').val(timeslot);
    $('#drawtime-display').html(time.calendar());
  });
</script>



<!-- PAYOUT MODAL -->

<script>
  $(document).ready(function() {

    // Shortcut key
    $(this).keyup(function (e) {
      if (e.ctrlKey && e.keyCode == 66) {
        $("#SearchWinResult").modal('toggle');
      }
    });
  });

</script>


<!-- ACCOUNT SETTINGS -->

<div id="Account-setting" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <form method="post" id="change-password-form" action="{{ route('change-password') }}">
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

<!-- PAYOUT -->

<div id="SearchWinResultAdmin" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" id="payout-form" action="{{ route('save-payout') }}">
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
                <input type="text" class="form-control" name="ticket-number" id="ticket-number" placeholder=""/>
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
        url: '{{ route('check-ticket') }}',
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

<!-- CANCEL TICKET -->

<script>
  $(document).ready(function() {

    // Shortcut key
    $(this).keyup(function (e) {
      if (e.ctrlKey && e.keyCode == 188) {
        $("#CancelTicket").modal('toggle');
      }
    });
  });

</script>

<div id="CancelTicket" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="post" id="cancel-form" action="{{ route('cancel-ticket') }}">
        <div class="modal-header">
          <h4 class="modal-title" id="myLargeModalLabel">Cancel Ticket</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          {{ csrf_field() }}
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" name="ticket-number" id="ticket-number-cancellation" placeholder=""/>
                <label for="CustomersName">Ticket ID</label>
              </div>
              <div class="form-group">
                <button type="button" class="btn btn-primary" id="ticket-search-for-cancellation" style="width:100%;">Check</button>
                <label style="padding-top: 20px;">Force Cancel
              		<input type="checkbox" name="force-cancel" id="force-cancel" value="true" />
              	</label>
              </div>
            </div>
            <div class="col-8">
              <table class="table">
                <thead>
                <tr>
                  <th>Bet</th>
                  <th>Type</th>
                  <th>Amount</th>
                  <th>Draw Date</th>
                  <th>Teller</th>
                  <th>Outlet</th>
                </tr>
                </thead>
                <tbody id="cancellation-search-results">
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
	$('#ticket-search-for-cancellation').click(function() {
		var ticketNumber = $('#ticket-number-cancellation').val();
		$.ajax({
	  	url: '{{ route('check-cancellation') }}',
	    type: 'post',
	    data: {'ticket-number': $('#ticket-number-cancellation').val(), 'force-cancel' : $('#force-cancel').is(':checked')},
	    headers: {
	    	'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
	    },
	    dataType: 'json',
	    cache: false,
	    success: function (cancel) {
	    	$('#cancellation-search-results').html('');
				if (typeof cancel.error !== 'undefined') {
					alert(cancel.error);
				} else {
					for (var c in cancel.bets) {
						var html = '<tr>';
						html += '<td>' + cancel.bets[c].bet + '</td>';
						html += '<td>' + cancel.bets[c].type + '</td>';
						html += '<td>' + cancel.bets[c].amount + '</td>';
						html += '<td>' + cancel.bets[c].draw_datetime + '</td>';
						html += '<td>' + cancel.bets[c].teller + '</td>';
						html += '<td>' + cancel.bets[c].outlet + '</td>';
						html += '</tr>';
						$('#cancellation-search-results').append(html);
					}
				}
	    },
	    error: function(e) {
		    alert(e);
	  	}
		});
	});
});
</script>

<div id="FilterTransaction" class="modal fade" tabindex="1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
			<form method="get" id="transaction-filter-form" action="{{ route('all-transactions') }}">
        <div class="modal-header">
          <h4 class="modal-title" id="myLargeModalLabel">Filter Transactions</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
					<div class="row">
            <div class="col-4">
              <div class="form-group">
                <input type="text" class="form-control" name="datefrom" id="datefrom" placeholder="YYYY-MM-DD" />
                <label for="datefrom">Date From</label>
              </div>
            </div>
            <div class="col-4">
							<div class="form-group">
                <input type="text" class="form-control" name="dateto" id="dateto" placeholder="YYYY-MM-DD" />
                <label for="datefrom">Date To</label>
              </div>
            </div>
            <div class="col-4">
            	<div class="form-group">
								<select name="outlet" class="form-control" id="outlet-filter">
									<option value="">Select an Outlet</option>
									@if (isset($outlets))
									@foreach ($outlets as $outlet)
									<option value="{{ $outlet->id }}">{{ $outlet->name }}</option>
									@endforeach
									@endif
								</select>
              </div>
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
	$('#datefrom').datepicker({format:'yyyy-mm-dd'});
	$('#dateto').datepicker({format:'yyyy-mm-dd'});
	
});
</script>
