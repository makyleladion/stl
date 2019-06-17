@extends('layouts.main') @section('content')


                    <!--Transactions Page starts-->                    
                    <section id="transactions-single-page">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="content-header">Transaction ID: <small>{{ $transaction->transactionNumber() }}</small></div>
                                <p class="content-sub-header">Total Bet Amount: &#8369; {{ number_format($transaction->amount(), 2, '.', ',') }} | {{ $transaction->transactionDateTime()->toDayDateTimeString() }}</p>
                            </div>                        
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">(STL-1) Pagente / {{ $row['teller'] }}</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <table class="table table-responsive-lg text-left">
                                                <thead>
                                                        <tr>
                                                            <th>Bet ID</th>
                                                            <th>Ticket ID</th>
                                                            <th>Game</th>                                                        
                                                            <th>Type</th>                                                            
                                                            <th>Bet Number</th>
                                                            <th>Bet Amount</th>
                                                            <th>Win Price</th>
                                                            <th>Cancelled</th>
                                                            <th>Draw Date/Time</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="text-justify">
                                                        @foreach ($transaction_array as $row)
                                                        <tr>
                                                            <td>{{ $row['transaction_id'] }}</td>
                                                            <td>{{ $row['ticket_number'] }}</td>
                                                            <td>{{ $row['bet_game'] }}</td>
                                                            <td>{{ $row['bet_type'] }}</td>
                                                            <td>{{ $row['bet_number'] }}</td>
                                                            <td>&#8369; {{ money_format('PHP %i', $row['bet_amount']) }}</td>
                                                            <td>&#8369; {{ number_format($row['bet_price'], 2, '.', ',') }}</td>
                                                            <td>{!! ($row['is_cancelled']) ? '<i class="icon icon-check"></i>' : '' !!}</td>
                                                            <td> @if ($row['draw_datetime_is_passed'])
                                                                <span class="text-warning">{{ $row['draw_datetime'] }}</span> @else
                                                                <span class="text-success">{{ $row['draw_datetime'] }}</span> @endif
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
                        <a href="{{ route('all-transactions')}}"><i class="icon-backburger"></i></a>
                    </div>

                    <div class="logo-text">
                        <div class="h4">Transaction No: {{ $transaction->transactionNumber() }}</div>
                        <div class="">Total Bet Amount: <b>PHP {{ number_format($transaction->amount(), 2, '.', ',') }}</b></div>
                        <div class="">Date/Time: {{ $transaction->transactionDateTime()->toDayDateTimeString() }}</div>
                    </div>

                </div>
            </div>
            <!-- / APP TITLE -->

            <!-- SEARCH -->
        </div>
        <!-- / HEADER -->

        <div class="page-content-card">
            <div id="e-commerce-orders-table_wrapper" class="dataTables_wrapper no-footer">
                <div class="dataTables_scroll">
                    <div class="dataTables_scrollBody">
                        <table id="e-commerce-orders-table" class="table dataTable">

                            <thead>

                                <tr>

                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">ID</span></div>
                                    </th>

                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Teller</span>
                                        </div>
                                    </th>

                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Customer Name</span>
                                        </div>
                                    </th>

                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Ticket No.</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Game</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Bet Amount</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Bet Number</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Type</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Win. Price</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Cancelled</span>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="table-header">
                                            <span class="column-title">Draw Date\Time</span>
                                        </div>
                                    </th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($transaction_array as $row)
                                <tr>
                                    <td>{{ $row['transaction_id'] }}</td>
                                    <td>{{ $row['teller'] }}</td>
                                    <td>{{ $row['customer_name'] }}</td>
                                    <td>
                                    @if (sha1(auth()->user()->email) === '5255b496f9cef19fbd07a117a1c6f7ef08d05649')
                                    <a href="{{ route('receipt', ['ticket_id' => $row['ticket_id']]) }}" target="_blank">{{ $row['ticket_number'] }}</a>
                                    @else
                                    {{ $row['ticket_number'] }}
                                    @endif
                                    </td>
                                    <td>{{ $row['bet_game'] }}</td>
                                    <td>{{ money_format('PHP %i', $row['bet_amount']) }}</td>
                                    <td>{{ $row['bet_number'] }}</td>
                                    <td>{{ $row['bet_type'] }}</td>
                                    <td>PHP {{ number_format($row['bet_price'], 2, '.', ',') }}</td>
                                    <td>{!! ($row['is_cancelled']) ? '<i class="icon icon-check"></i>' : '' !!}</td>
                                    <td>
                                        @if ($row['draw_datetime_is_passed'])
                                        <span class="text-warning">{{ $row['draw_datetime'] }}</span> @else
                                        <span class="text-success">{{ $row['draw_datetime'] }}</span> @endif
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
    <script type="text/javascript" src="{{ url('/assets/js/apps/e-commerce/orders/orders.js')}}"></script>

    <!-- / CONTENT -->
</div>

@endsection
