@extends('layouts.main') @section('content')

<div id="project-dashboard" class="page-layout blank p-6">

    <div class="page-content-wrapper">

        <!-- CONTENT -->
        <div class="page-content">

            @if (\Session::has('outlet-dashboard-success'))
            <div class="alert alert-success" role="alert">{{ session('outlet-dashboard-success') }}</div>
            @endif @if (\Session::has('error-flash'))
            <div class="alert alert-danger" role="alert">{{ session('error-flash') }}</div>
            @endif @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- WIDGET GROUP 1 -->
            <div class="widget-group row">

                <!-- WIDGET 1 -->
                @foreach (\App\System\Data\Timeslot::drawTimeslots() as $sched_key => $timeslot)
                <div class="col-12 col-sm-6 col-xl-4 p-4">

                    @if (\App\System\Utils\TimeslotUtils::isDrawTimePassed(date('Y-m-d'), $sched_key, \App\System\Utils\TimeslotUtils::globalCutOffTime()))
                    <div class="widget widget1 card bg-danger">
                        @elseif (\App\System\Utils\TimeslotUtils::isDrawTimePassed(date('Y-m-d'), $sched_key, \App\System\Utils\TimeslotUtils::globalPrepareToCutOffTime()))
                        <div class="widget widget1 card bg-warning">
                            @else
                            <div class="widget widget1 card">
                                @endif

                                <div class="widget-header pl-4 pr-2 row no-gutters align-items-center justify-content-between">

                                    <div class="col">
                                        <span class="h4">{{ date('g:ia', strtotime($timeslot)) }}</span>
                                    </div>

                                    <button type="button" class="btn btn-icon btn-fab btn-sm fuse-ripple-ready daily-sales-popup" data-toggle="modal" data-target="#DailySalesReport" data-schedule="{{ $timeslot }}">
                                        <i class="icon icon-printer"></i>
                                    </button>

                                </div>

                                <div class="widget-content pt-2 pb-8 d-flex flex-column align-items-center justify-content-center">
                                    <div class="title text-primary" id="ticket_counts_bet_schedule_morning">{{ $number_of_tickets[$sched_key] }}</div>
                                    <div class="sub-title h6 text-muted">No. of tickets</div>
                                </div>

                                <div class="widget-footer p-4 bg-faded row no-gutters align-items-center">
                                    @foreach ($winnings[$sched_key] as $game => $result)
                                    <span class="text-muted">{{ $game }}:</span>
                                    <span class="ml-2" id="">{{ str_replace(':','-',$result) }}</span>
                                    <span class="ml-5"></span> @endforeach
                                    <span class="text-muted">Draw Sales:</span>
                                    <span class="ml-2" id=""><b>PHP {{ number_format($daily_sales[$sched_key], 2, '.', ',') }}</b></span>
                                    <span class="ml-5"></span>
                                    <span class="text-muted">No. of Winners:</span>
                                    <span class="ml-2" id="">{{ $number_of_winnings[$sched_key] }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <!-- / WIDGET 1 -->

                    </div>

                    <!-- WIDGET GROUP 2 -->
                    <div class="widget-group row">
          
                        <!-- WIDGET 2 -->
                        <div class="col-12 col-lg-12 p-3">

                            <div class="widget widget6 card">

                                <div class="widget-header px-4 row no-gutters align-items-center justify-content-between">

                                    <div class="col">
                                        <span class="h6">Daily Payouts - <b>{{ $outlet_name }}</b></span>
                                    </div>

                                    <button type="button" class="btn btn-icon" data-toggle="modal" data-target="#SearchWinResult">
                                        <i class="icon icon-person-plus"></i>
                                    </button>

                                </div>

                                <div class="widget-content">
                                    <table class="table table-responsive">
                                        <thead>
                                            <tr>
                                                <th> Ticket No. </th>
                                                <th> Bet </th>
                                                <th> Winning Price </th>
                                                <th> Draw Date </th>
                                                <th> Payout Date </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($payouts as $payout)
                                            <tr>
                                                <td>{{ $payout->ticketNumber() }}</td>
                                                <td>{{ $payout->bet()->betNumber() }}</td>
                                                <td>PHP {{ number_format($payout->bet()->price(), 2, '.', ',') }}</td>
                                                <td>{{ $payout->drawDateTime()->toDayDateTimeString() }}</td>
                                                <td>{{ $payout->payoutDateTime()->toDayDateTimeString() }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                  <div id="e-commerce-orders-table_wrapper" class="dataTables_wrapper no-footer">
                                <div class="dataTables_paginate paging_simple_numbers" id="e-commerce-orders-table_paginate">
                    <a href="#" class="paginate_button previous" id="e-commerce-orders-table_previous">Previous</a> 
                     
                    <a href="#" class="paginate_button current" aria-controls="e-commerce-orders-table" data-dt-idx="1" tabindex="0">1</a>
                    <a href="#" class="paginate_button next" aria-controls="e-commerce-orders-table" data-dt-idx="4" tabindex="0" id="e-commerce-orders-table_next">Next</a>                  
                </div>
                  </div>

                            </div>
                        </div>
                  
                        <!-- / WIDGET 2 -->

                        <!-- WIDGET 3 -->
                        <div class="col-12 col-lg-4 p-3" style="display:none;">

                            <div class="widget widget-7 card">

                                <div class="widget-header px-4 row no-gutters align-items-center justify-content-between">

                                    <div class="col">
                                        <span class="h6">Notes &amp; Reminders</span>
                                    </div>

                                    <button type="button" class="btn btn-icon">
                                        <i class="icon icon-person-plus"></i>
                                    </button>
                                </div>

                                <div class="widget-content p-4">

                                    <div class="py-4 row no-gutters align-items-center justify-content-between">

                                        <div class="col">

                                            <div class="h6">Daily Sales Bank Deposit Slip</div>

                                            <div>
                                                <span class="text-muted">11:00 AM</span>

                                            </div>

                                        </div>

                                        <div class="col-auto">
                                            <button type="button" class="btn btn-icon">
                                                <i class="icon icon-dots-vertical"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- / WIDGET 3 -->
                             <div class="memo">
                  <!-- <a id="action-on-bottom-snackbar" class="botonF1 btn btn-danger btn-fab fuse-ripple-ready"><i class="icon-message-alert"></i></a> -->

                    <script type="text/javascript">
                        $('#action-on-bottom-snackbar').on('click', function ()
                        {
                            new PNotify({
                                text    : 'Memo From 3A8: </br> All Outlets 45minutes before draw time should remit the draw sales.',
                                confirm : {
                                    confirm: true,
                                    buttons: [
                                        {
                                            text    : 'Dismiss',
                                            addClass: 'btn btn-link',
                                            click   : function (notice)
                                            {
                                                notice.remove();
                                            }
                                        },
                                        null
                                    ]
                                },
                                buttons : {
                                    closer : false,
                                    sticker: false
                                },
                                animate : {
                                    animate  : true,
                                    in_class : 'slideInDown',
                                    out_class: 'slideOutUp'
                                },
                                addclass: 'md multiline action-on-bottom'
                            });
                        });
                    </script>
                        </div>
												@if ($current_print)
                        <div class="contenedor">
                            <a href="{{ route('multiple-receipts', ['tickets_str' => $current_print, 'pos' => 0]) }}" class="botonF1 btn btn-danger btn-fab fuse-ripple-ready" data-toggle="tooltip" data-placement="left" title="" data-original-title="Reprint Tickets">
                                <i class="icon-printer"></i>
                            </a>
                        </div>
                        @endif
                    </div>

                </div>
                <!-- / WIDGET GROUP -->
            </div>

        </div>

        @include('inc.modal-forms') @endsection
