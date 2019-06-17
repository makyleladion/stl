<script>
  function {{ $game->name() }}TypeInput() {
    return $('#swertres-stl-local-betType').val();
  }
  function {{ $game->name() }}BetNumber() {
    return $('#swertres-stl-local-number').val();
  }
  function {{ $game->name() }}Amount() {
    return $('#swertres-stl-local-amount').val();
  }
  function {{ $game->name() }}Winning(betNumber, type, amount, schedTime, cb, outlet_id) {
	var dataToSend = {'game': '{{ $game->name() }}','betNumber': betNumber, 'type': type, 'amount':amount, 'sched_time': schedTime };
	@if (isset($outlet_id))
	var calcUrl = '{{ route('outlet-calc', ['outlet_id' => $outlet_id]) }}';
	@else
	var calcUrl = '{{ route('outlet-calc', ['outlet_id' => 0]) }}';
	calcUrl = calcUrl.replace("0", outlet_id);
	@endif
    $.ajax({
      url: calcUrl,
      type: 'post',
      data: dataToSend,
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      dataType: 'json',
      success: function (data) {
        cb(data);
      },
      error: function(e) {
        console.log(e);
        alert('Error while calculating Swertres Local winnings.');
      }
    });
  }
</script>
<script>
function checkForUnique(str) {
    var len = str.length;
    for(var i=0;i<len;i++){
        var temp = str[i];
        for(var x=i+1;x<= len -1; x++){
            if (temp == str[x]){
                return false;
            }
        }
         
    }               
    return true;
}
$(document).ready(function() {
	$('#swertres-stl-local-betType').change(function() {
		if ($(this).val() == 'rambled') {
			$('#swertres-stl-local-multiplier-container').show();
			$('#swertres-stl-local-amount').prop('disabled', true);
		} else {
			$('#swertres-stl-local-multiplier-container').hide();
			$('#swertres-stl-local-amount').prop('disabled', false);
		}
		$('#swertres-stl-local-amount').val('');
	});

	$('#swertres-stl-local-multiplier').change(function() {
		if ($('#swertres-stl-local-number').val().length < 3) {
			alert('Please enter a correct bet before calculating ramble amount.');
		} else if ($(this).val() <= 0) {
			alert('Please select a valid ramble multiplier');
		} else {
			var bet = $('#swertres-stl-local-number').val();
			var m = checkForUnique(bet) ? 6 : 3;
			var r = $(this).val();
			var a = m * r;
			$('#swertres-stl-local-amount').val(a);
		}
		this.selectedIndex = "0";
	});

	$('#swertres-stl-local-number').change(function() {
		if ($(this).val().length <= 0) {
			$('#swertres-stl-local-amount').val('');
		} 
	});
});
</script>
<div class="swertres_stl_local box">
    <div class="container-form">
        <div class="form-group col-md-4">
            <label for="Number" class="col-form-label">Number</label>
            <input type="text" class="form-control" id="swertres-stl-local-number" required placeholder="###" maxlength="3" />
        </div>
        <div class="form-group col-md-4">
            <label for="BetType" class="col-form-label">Bet Type</label>
            <select class="form-control" id="swertres-stl-local-betType">
              @foreach ($game->betTypes() as $type)
                <option value="{{ $type }}">{{ ucfirst($type) }}</option>
              @endforeach
            </select>
        </div>
        <div class="form-group col-md-4">
            <label for="Amount" class="col-form-label">Amount</label>
            <input type="text" class="form-control" id="swertres-stl-local-amount" required placeholder="###" />
        </div>
    </div>
    <div class="container-form" id="swertres-stl-local-multiplier-container" style="display:none">
    		<div class="form-group col-md-12">
            <label for="swertres-stl-local-multiplier" class="col-form-label">Multiplier</label>
            <select id="swertres-stl-local-multiplier" class="form-control">
            <option value="0">Select Multiplier</option>
            <?php for ($i = 1; $i <= 50; $i++): ?>
            <option value="{{$i}}">Ramble {{$i}}</option>
            <?php endfor; ?>
            </select>
        </div>
    </div>
</div>
