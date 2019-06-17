<script>
  function {{ $game->name() }}TypeInput() {
    return 'none';
  }
  function {{ $game->name() }}BetNumber() {
    return $('#{{ $game->name() }}-number-1').val() + ':' + $('#{{ $game->name() }}-number-2').val();
  }
  function {{ $game->name() }}Amount() {
    return $('#{{ $game->name() }}-amount').val();
  }
  function {{ $game->name() }}Winning(betNumber, type, amount, schedTime, cb, outlet_id) {
	  @if (isset($outlet_id))
	  var calcUrl = '{{ route('outlet-calc', ['outlet_id' => $outlet_id]) }}';
	  @else
		var calcUrl = '{{ route('outlet-calc', ['outlet_id' => 0]) }}';
		calcUrl = calcUrl.replace("0", outlet_id);
	  @endif
    $.ajax({
      url: calcUrl,
      type: 'post',
      data: {'game': '{{ $game->name() }}','betNumber': betNumber, 'type': type, 'amount':amount, 'sched_time': schedTime },
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      dataType: 'json',
      success: function (data) {
        cb(data);
      },
      error: function(e) {
        alert('Error while calculating Pares winnings.');
      }
    });
  }
</script>
<div class="pares box">
    <div class="container-form">
        <div class="form-group col-md-4">
            <label for="Number" class="col-form-label">First No.</label>
            <input type="text" class="form-control" id="pares-number-1" required placeholder="##" maxlength="2" />
        </div>
        <div class="form-group col-md-4">
            <label for="Number" class="col-form-label">Second No.</label>
            <input type="text" class="form-control" id="pares-number-2" required placeholder="##" maxlength="2" />
        </div>
        <div class="form-group col-md-4">
            <label for="Amount" class="col-form-label">Amount</label>
            <input type="text" class="form-control" id="pares-amount" required placeholder="###" />
        </div>
    </div>
</div>
