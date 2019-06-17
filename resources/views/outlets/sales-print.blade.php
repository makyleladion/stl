@extends('layouts.main') 

@section('content')

<div id="invoice-POS">

    <script type="text/javascript">
     window.onload = function() { window.print(); }
    </script>

      <center id="top">
          <div class="logo"><img src="{{url('assets/images/smalltownlottery.png')}}" style="width: 90px;"></div>
          <div class="info"> 
            <h2>Small Town Lottery - Iligan</h2>
            <h6 style="margin:10px 0;">{{ $transaction->outletName() }}</h6>
          </div><!--End Info-->
      </center><!--End InvoiceTop-->
      
      <div id="mid">
          <div class="info">
            <h2>Draw: {{ $ticket->drawDateTimeCarbon()->toDayDateTimeString() }}</h2>
            <h2 style="margin:10px 0;">Name: {{ $transaction->customerName() }}</h2>
            <p> 
                TICKET ID : {{ $ticket->ticketNumber() }}</br>
                BET DATE   : {{ $ticket->betDateTime()->toFormattedDateString() }}</br>
                BET TIME   : {{ $ticket->betDateTime()->format('h:i A') }}</br>
            </p>
          </div>
      </div><!--End Invoice Mid-->
      
      <div id="bot">
      <div id="table">
        <table>
          <tr class="tabletitle">
            <td class="item"><h2>No.</h2></td>
            <td class="Type"></td>
            <td class="Hours text-right"><h2>Amt</h2></td>
            <td class="Rate text-right"><h2>Win</h2></td>
          </tr>

          @foreach ($ticket->bets() as $bet)
          <tr class="service">
            <td class="tableitem"><p class="itemtext">{!! str_replace(':', ' ', $bet->betNumber(true)) !!}</p></td>
            <td class="tableitem"><p class="itemtext">{{ $bet->betTypeAbbreviation() }}</p></td>
            <td class="tableitem"><p class="itemtext text-right">{{ $bet->amount() }}</p></td>
            <td class="tableitem"><p class="itemtext text-right">{{ number_format($bet->price(), 2, '.', ',') }}</p></td>
          </tr>
          @endforeach

          <tr class="tabletitle">            
            <td class="Rate"><h2>Total Amount:</h2></td>                        
            <td></td>
            <td class="payment"><h2 class="itemtext text-right">{{ number_format($ticket->amount(), 2, '.', ',') }}</h2></td>
            <td></td>
          </tr>
        </table>   
        </p>
      </div><!--End Table-->
      <div id="txnCode">
        <p><strong>TXN Code: {{ $transaction->transactionNumber() }}</strong><p>
      </div>
      <div id="legalcopy">
        <p class="legal">Winning ticket should be claimed within a year after the bet date, otherwise winning prize shall be forfieted. </p>            
      </div>

    </div><!--End InvoiceBot-->
  </div><!--End Invoice-->


  @if(isset($tickets_str) && isset($next_page))
    <script>
      $(document).ready(function() {
        @if (is_numeric($next_page))
        setTimeout(function() {
          window.location.replace("{{ route('multiple-receipts', ['tickets_str' => $tickets_str, 'current_id' => $next_page]) }}");
        }, 3000);
        @else
        setTimeout(function() {
          window.location.replace("{{ route('outlet-dashboard', ['outlet_id' => $ticket->getOutlet()->id]) }}");
        }, 3000);
        @endif
      });
    </script>
  @endif

@endsection
