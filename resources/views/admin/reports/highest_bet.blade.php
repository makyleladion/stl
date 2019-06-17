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
                        <div class="h4">Highest Bets as of <b>today</b>.</div>
                        @else
                        <div class="h4">Highest Bets as of <b>{{ date('l, F j, Y', strtotime($draw_date)) }}</b>.</div>
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

                    <div class="col-sm-3" style="margin-top: 10px;">
                        <input type="text" id="time-machine-datepicker" class="h6 custom-select form-control" placeholder="Pick a Date" value="{{$draw_date}}">
                        <script>
                            $(document).ready(function() {
                                $('#time-machine-datepicker').datepicker({format:'yyyy-mm-dd'});
                                $('#time-machine-datepicker').change(function() {
                                    var url = "{{ route('reports-highest-bet') }}/" + $(this).val();
                                    window.location.href = url;
                                });
                            });
                            </script>
                    </div>         

                    <!-- <div class="col-3">
                        <button id="exportButton" class="btn btn-secondary btn-fab btn-sm" role="button" aria-pressed="true"><i class="icon icon-printer"></i></button>                        
                    </div>  -->

                </div>  

            </div>
            <!-- / CONTENT TOOLBAR -->

            <div id="e-commerce-orders-table_wrapper" class="dataTables_wrapper no-footer">
               <div class="dataTables_scroll">
                  <div class="dataTables_scrollBody">
                    <table id="e-commerce-orders-table" class="table table-hover dataTable">
                      <thead>
                          <tr>
                          	<th>Ticket No.</th>
                            <th>Outlet</th>
                            <th>Teller</th>
                            <th>Customer</th>
                            <th>Bet</th>
                            <th>Game</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Win Amt</th>
                            <th>Draw Date</th>
                          </tr>
                      </thead>
                      <tbody>
											@foreach ($highest_winnings as $hwinner)
                      <tr>
                      	<td><span class="">{{ $hwinner->ticketNumber() }}</span></td>
                        <td><span class="">{{ $hwinner->outletName() }}</span></td>
                        <td><span>{{ $hwinner->teller() }}</span></td>
                        <td><span>{{ $hwinner->customer() }}</span></td>
                        <td><span>{{ $hwinner->bet() }}</span></td>
                        <td><span>{{ $hwinner->game() }}</span></td>
                        <td><span>{{ $hwinner->type() }}</span></td>
                        <td><span>PHP {{ number_format($hwinner->amount(), 2, '.', ',') }}</span></td>
                        <td><span>PHP {{ number_format($hwinner->winningPrize(), 2, '.', ',') }}</span></td>
                        <td><span>{{ $hwinner->drawDateTimeCarbon()->toDayDateTimeString() }}</span></td>
                      </tr>
                      @endforeach
                      </tbody>
                    </table>
                  </div>
                </div>
            </div>

        </div>
    </div>
    <!-- / CONTENT -->
</div>

@endsection
