@extends('layouts.main') 

@section('content')

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
                        <div class="h4">Payouts</div>
                        <div class="">Total Payouts: {{ $total_payouts }}</div>
                    </div>

                </div>
            </div>
            <!-- / APP TITLE -->

            <!-- SEARCH -->
            <div class="col search-wrapper pl-2">

                <div class="input-group">

                    <span class="input-group-btn">
                        <button type="button" class="btn btn-icon">
                            <i class="icon icon-magnify"></i>
                        </button>
                    </span>

                    <input id="orders-search-input" type="text" class="form-control" placeholder="Search" aria-label="Search" />

                </div>
            </div>
            <!-- / SEARCH -->
        </div>
        <!-- / HEADER -->

        <div class="page-content-card">
            <div id="e-commerce-orders-table_wrapper" class="dataTables_wrapper no-footer">
            <table id="e-commerce-orders-table" class="table dataTable">

                <thead>

                    <tr>

                        <th>
                            <div class="table-header">
                                <span class="column-title">Teller</span></div>
                        </th>

                        <th>
                            <div class="table-header">
                                <span class="column-title">Ticket No.</span>
                            </div>
                        </th>

                        <th>
                          <div class="table-header">
                            <span class="column-title">Customer Name</span>
                          </div>
                        </th>

                        <th>
                          <div class="table-header">
                            <span class="column-title">Bet</span>
                          </div>
                        </th>

                        <th>
                          <div class="table-header">
                            <span class="column-title">Type</span>
                          </div>
                        </th>

                        <th>
                          <div class="table-header">
                            <span class="column-title">Amount</span>
                          </div>
                        </th>

                        <th>
                            <div class="table-header">
                                <span class="column-title">Winning Price</span>
                            </div>
                        </th>

                        <th>
                            <div class="table-header">
                                <span class="column-title">Draw Date</span>
                            </div>
                        </th>

                        <th>
                            <div class="table-header">
                                <span class="column-title">Payout Date</span>
                            </div>
                        </th>

                    </tr>
                </thead>

                <tbody>
                    @foreach ($payouts as $payout)
                    <tr>
                        <td>{{ $payout->teller() }}</td>
                        <td>{{ $payout->ticketNumber() }}</td>
                        <td>{{ $payout->customerName() }}</td>
                        <td>{{ $payout->bet()->betNumber() }}</td>
                        <td>{{ $payout->bet()->betType() }}</td>
                        <td>PHP {{ number_format($payout->bet()->amount(), 2, '.', ',') }}</td>
                        <td>PHP {{ number_format($payout->bet()->price(), 2, '.', ',') }}</td>
                        <td>{{ $payout->drawDateTime()->toDayDateTimeString() }}</td>
                        <td>{{ $payout->payoutDateTime()->toDayDateTimeString() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
                <div class="dataTables_info" id="e-commerce-orders-table_info" role="status" aria-live="polite">
                      <div id="pagi_page-content" class="pagi_page-content">Page {{ $page }} of {{ $total_pages }}</div>
                </div>
               <div class="dataTables_paginate paging_simple_numbers" id="e-commerce-orders-table_paginate">
                    <a href="{{ route('all-payouts', ['page' => $prev]) }}" class="paginate_button previous" id="e-commerce-orders-table_previous">Previous</a>
                    <a href="{{ route('all-payouts', ['page' => $next]) }}" class="paginate_button next" aria-controls="e-commerce-orders-table" data-dt-idx="4" tabindex="0" id="e-commerce-orders-table_next">Next</a>           
                </div>

       
                </div>
                    </div>
    </div>
    <!-- / CONTENT -->
</div>

<script type="text/javascript" src="{{ url('/assets/js/apps/e-commerce/orders/orders.js?v=1')}}"></script>

@endsection
