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
                        <div class="h4">Invalidated Tickets From Offline Mobile Transactions</div>
                        <div class="">Total Transactions: {{ count($invalid_tickets) }}</div>
                    </div>

                </div>
            </div>
            <!-- / APP TITLE -->

        </div>
        <!-- / HEADER -->

        <div class="page-content-card">

            <div class="toolbar p-4">
                <div class="row">

                    <div class="col-sm-3" style="margin-top: 10px;">
                        <input type="text" id="date-from-machine-datepicker" class="h6 custom-select form-control" placeholder="Pick a Date" value="{{$date_from}}">
                        <script>
                            $(document).ready(function() {
                                $('#date-from-machine-datepicker').datepicker({format:'yyyy-mm-dd'});
                                $('#date-from-machine-datepicker').change(function() {
                                    var dateFrom = $(this).val();
                                    var dateTo = $('#date-to-machine-datepicker').val();
                                    var url = "{{ route(\Route::current()->getName()) }}/" + dateFrom + "/" + dateTo;

                                    var dateFromTimestamp = new Date(dateFrom).getTime();
                                    var dateToTimestamp = new Date(dateTo).getTime();

                                    if (dateFromTimestamp > dateToTimestamp) {
                                        alert('Please select a Date From value lower than the Date To.');
                                    } else {
                                        window.location.href = url;
                                    }
                                });
                            });
                            </script>
                    </div>
                    
                    <div class="col-sm-3" style="margin-top: 10px;">
                        <input type="text" id="date-to-machine-datepicker" class="h6 custom-select form-control" placeholder="Pick a Date" value="{{$date_to}}">
                        <script>
                            $(document).ready(function() {
                                $('#date-to-machine-datepicker').datepicker({format:'yyyy-mm-dd'});
                                $('#date-to-machine-datepicker').change(function() {
                                        var dateFrom = $('#date-from-machine-datepicker').val();
                                    var dateTo = $(this).val();
                                    var url = "{{ route(\Route::current()->getName()) }}/" + dateFrom + "/" + dateTo;

                                    var dateFromTimestamp = new Date(dateFrom).getTime();
                                    var dateToTimestamp = new Date(dateTo).getTime();

                                    if (dateFromTimestamp > dateToTimestamp) {
                                        alert('Please select a Date From value that is lower than the Date To.');
                                    } else {
                                        window.location.href = url;
                                    }
                                });
                            });
                            </script>
                    </div>
                </div>
            </div>
            

            <div id="e-commerce-orders-table_wrapper" class="dataTables_wrapper no-footer">
                <div class="dataTables_scroll">

                    <div class="dataTables_scrollBody">
                         <table id="e-commerce-orders-table" class="table dataTable">

                                <thead>

                                    <tr>
                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Date Created</span>
                                            </div>
                                        </th>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Transaction Code</span>
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
                                                <span class="column-title">Amount</span>
                                            </div>
                                        </th>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Result Date</span>
                                            </div>
                                        </th>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Reason</span></div>
                                        </th>

                                        <th>
                                            <div class="table-header">
                                                <span class="column-title">Source</span></div>
                                        </th>

                                    </tr>
                                </thead>

                            <tbody>
                                @foreach ($invalid_tickets as $invalid_ticket)
                                <tr>
                                    <td>{{ $invalid_ticket['created_at'] }}</td>
                                    <td>{{ $invalid_ticket['transaction_code'] }}</td>
                                    <td>{{ $invalid_ticket['ticket_number'] }}</td>
                                    <td><a href="{{ route('outlet-dashboard', ['outlet_id' => $invalid_ticket['outlet_id'] ]) }}">{{$invalid_ticket['outlet']['name'] }}</a></td>
                                    @if ($invalid_ticket['user']!=null)
                                        <td><a href="{{ route('edit-user', ['user_id' => $invalid_ticket['user']['id'] ]) }}">{{ $invalid_ticket['user']['name'] }}</td>
                                    @else
                                        <td>UNKNOWN</td>
                                    @endif
                                    <td>{{ $invalid_ticket['customer'] }}</td>
                                    <td>{{ $invalid_ticket['amount'] }}</td>
                                    <td>{{ $invalid_ticket['result_date'] }}</td>
                                    <td>{{ $invalid_ticket['error'] }}</td>
                                    <td>{{ $invalid_ticket['source'] }}</td>
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

<script type="text/javascript" src="{{ url('/assets/js/apps/e-commerce/orders/orders.js?v=1')}}"></script>

@endsection
