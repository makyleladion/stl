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
            
            		@if (\Session::has('error-flash'))
                <div class="alert alert-danger" role="alert">{{ session('error-flash') }}</div>
                @endif

                <div class="logo row no-gutters align-items-start">

                    <div class="logo-icon mr-3 mt-1">
                        <i class="icon-cards-outline s-6"></i>
                    </div>

                    <div class="logo-text">
                        <div class="h4">Transactions with Canceled Tickets</div>
                        <div class="">Total Transactions: {{ $total_transactions }}</div>
                    </div>

                </div>
            </div>
            <!-- / APP TITLE -->

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
                                                <span class="column-title">Transaction ID</span>
                                            </div>
                                        </th>
                                        
                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Ticket ID</span>
                                            </div>
                                        </th>

                                        <th>
                                          <div class="table-header">
                                            <span class="column-title">Outlet</span>
                                          </div>
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
                                                <span class="column-title">Bets</span>
                                            </div>
                                        </th>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Canceled By</span>
                                            </div>
                                        </th>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Cancel Date</span>
                                            </div>
                                        </th>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Actions</span></div>
                                        </th>

                                    </tr>
                                </thead>

                            <tbody>
                                @foreach ($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->id(true) }}</td>
                                    <td>{{ $transaction->transactionNumber() }}</td>
                                    <td>{!! $transaction->tickets(true) !!}</td>
                                    <td><a href="{{ route('outlet-dashboard', ['outlet_id' => $transaction->getOutlet()->id]) }}">{{ $transaction->outletName() }}</a></td>
                                    <td><a href="{{ route('edit-user', ['user_id' => $transaction->tellerObj()->id]) }}">{{ $transaction->teller() }}</a></td>
                                    <td>{{ $transaction->customerName('none') }}</td>
                                    <td>{{ $transaction->betsString() }}</td>
                                    <td>{{ $transaction->canceledBy() }}</td>
                                    <td>{{ $transaction->cancelDates() }}</td>
                                    <td>
                                        <a href="{{ route('single-transaction', ['transaction_id' => $transaction->getTransaction()->id, 'outlet_id' => $transaction->getOutlet()->id]) }}" class="btn btn-default btn-sm">View</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                         </table>
                    </div>
                </div>
                <div class="dataTables_info" id="e-commerce-orders-table_info" role="status" aria-live="polite">
                      <div id="pagi_page-content" class="pagi_page-content">Page {{$page}} of {{$total_pages}}</div> 
                </div>
                <div class="dataTables_paginate paging_simple_numbers" id="e-commerce-orders-table_paginate">
               		<a href="{{ route('all-transactions-canceled', ['page' => $prev]) }}{{ !is_null($query) ? '?' . $query : '' }}" class="paginate_button previous" id="e-commerce-orders-table_previous">Previous</a>
                	<a href="{{ route('all-transactions-canceled', ['page' => $next]) }}{{ !is_null($query) ? '?' . $query : '' }}" class="paginate_button next" aria-controls="e-commerce-orders-table" data-dt-idx="4" tabindex="0" id="e-commerce-orders-table_next">Next</a>
                </div>
            </div>

        </div>
    </div>
    <!-- / CONTENT -->
</div>

<script type="text/javascript" src="{{ url('/assets/js/apps/e-commerce/orders/orders.js?v=1')}}"></script>
@include('inc.winningResult')
@endsection
