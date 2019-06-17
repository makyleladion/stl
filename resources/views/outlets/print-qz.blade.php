<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title></title>
</head>
<body>
<div id="invoice-POS" style="width:58mm;margin-left:-2.5mm">
<center id="top" style="margin-left:-9.0mm">
        <div class="logo" style="padding:0px"><img src="{{url('assets/images/smalltownlottery.png')}}" style="width: 50px; padding-right: 10px;"><img src="{{url('assets/images/3a8_logo.png')}}" style="width: 50px;"></div>
        <div class="info">
            <h2 style="font-size:.6em;">Small Town Lottery - Iligan</h2>
            <h6 style="font-size:.8em">{{ $transaction->outletName() }}</h6>
        </div>
        <!--End Info-->
    </center>
    <!--End InvoiceTop-->

    <div id="mid" style="width:58mm;">
        <div class="info" style="font-size:10.5px">
            <h3 style="font-size:10.5px">Draw: {{ $ticket->drawDateTimeCarbon()->toDayDateTimeString() }}</h3>
            <h2 style="margin:10px 0;"></h2>
            <h3 style="text-align: left;font-size:10.5px">
                NAME: {{ $transaction->customerName() }}</br>
                TICKET ID : {{ $ticket->ticketNumber() }}</br>
                BET DATE : {{ $ticket->betDateTime()->toFormattedDateString() }}</br>
                BET TIME : {{ $ticket->betDateTime()->format('h:i A') }}</br>
            </h3>
        </div>
    </div>
    <!--End Invoice Mid-->

    <div id="bot">
        <div id="table">
            <table style="font-size:.55em;width:55mm;">
                <tr class="tabletitle" style="width:55mm;">
                    <td class="item" style="width:10mm;">
                        <h2>No.</h2></td>
                    <td class="Type" style="width:10mm;"></td>
                    <td class="Hours text-right" style="width:10mm;">
                        <h2>Amt</h2></td>
                    <td class="Rate text-right" style="width:10mm;">
                        <h2>Win</h2></td>
                    <td></td>
                </tr>

                @foreach ($ticket->bets() as $bet)
                <tr class="service">
                    <td class="tableitem" style="width:10mm;">
                        <p  style="font-size:1.2em;font-weight: bold;">{!! str_replace(':', ' ', $bet->betNumber(true)) !!}</p>
                    </td>
                    <td class="tableitem" style="width:10mm;">
                        <p  style="font-size:1.2em;font-weight: bold;">{{ $bet->betTypeAbbreviation() }}</p>
                    </td>
                    <td class="tableitem" style="width:10mm;">
                        <p class="text-right" style="font-size:1.2em;font-weight: bold;">{{ $bet->amount() }}</p>
                    </td>
                    <td class="tableitem" style="width:10mm;">
                        <p class=" text-right" style="font-size:1.2em;font-weight: bold;">{{ number_format($bet->price(), 2, '.', ',') }}</p>
                    </td>
                    <td>
                    </td>
                </tr>
                @endforeach
                <tr></tr>
                <tr class="tabletitle">
                    <td class="Rate" style="width:10mm;">
                        <h2>Total Amount:</h2></td>
                    <td style="width:10mm;"></td>
                    <td style="width:10mm;"></td>
                    <td class="payment" style="width:10mm;">
                        <h2 class="itemtext text-right">{{ number_format($ticket->amount(), 2, '.', ',') }}</h2></td>
                    <td></td>
                </tr>
            </table>
            </p>
        </div>
        <!--End Table-->
        <div id="txnCode" style="font-size:.7em;">
            <p><strong>TXN Code: {{ $transaction->transactionNumber() }}</strong>
                <p>
        </div>
        <div id="legalcopy" style="margin-right: 20px">
            <span class="legal" style="font-size:.8em;">Winning ticket should be claimed within a year after the bet date, otherwise winning prize shall be forfeited. </span>
        </div>

    </div>
    <!--End InvoiceBot-->
</div>
<!--End Invoice-->
</body>
</html>
