@extends('layouts.mail2')

@section('content')
@foreach (\App\System\Data\Timeslot::drawTimeslots() as $sched_key => $timeslot)
<h3>Sales as of {{ date('g:ia', strtotime($timeslot)) }}</h3>
<table style="width:100%">
  <tr>
    <td>Sales</td>
    <td style="text-align:right">PHP {{ number_format($daily_sales[$sched_key], 2, '.', ',') }}</td>
  </tr>
  @foreach ($winnings[$sched_key] as $game => $result)
  <tr>
    <td>Game: {{ $game }}</td>
    <td style="text-align:right">{{ str_replace(':','-',$result) }}</td>
  </tr>
  @endforeach
  <tr>
  	<td>No. of tickets</td>
  	<td style="text-align:right">{{ $number_of_tickets[$sched_key] }}</td>
  </tr>
  <tr>
  	<td>No. of Winners:</td>
  	<td style="text-align:right">{{ $number_of_winnings[$sched_key] }}</td>
  </tr>
</table>
@endforeach

<table style="margin-top:20px;width:100%">
	<tr>
    <td><b>Total Sales</b></td>
    <td style="text-align:right"><b>PHP {{ number_format($total_amount, 2, '.', ',') }}</b></td>
  </tr>
</table>

<h3>Total Sales Per Game</h3>

<table style="margin-top:20px;width:100%">
    <thead>
        <tr>
            <td>Draw Time</td>
            @foreach($daily_aggregated_sales_headers as $gameLabel)
            <td>{{ $gameLabel }}</td>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($daily_aggregated_sales as $das)
        <tr>
            <td>{{ $das['drawtime'] }}</td>
            @foreach($das['games'] as $gameSale)
            <td><span>PHP {{ number_format($gameSale, 2, '.', ',') }}</span></td>
            @endforeach
        </tr>
        @endforeach
        <tr>
            <td>Total</td>
            @foreach($daily_aggregated_sales_totals as $gameSaleTotal)
            <td><span>PHP {{ number_format($gameSaleTotal, 2, '.', ',') }}</span></td>
            @endforeach
        </tr>
    </tbody>
</table>

<h3>Payouts (Total: PHP {{ number_format($winning_allocation, 2, '.', ',') }})</h3>

<table style="width:100%">
	@foreach($winners_aggregated as $key => $drawTimeData)
	<tr>
		<td>{{ date('g A', strtotime($key)) }}</td>
		<td>PHP {{ number_format($drawTimeData['total_amount'], 2, '.', ',') }}</td>
	</tr>
	@endforeach
</table>
@endsection