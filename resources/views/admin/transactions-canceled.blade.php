            @extends('layouts.main')
                @section('content')
                    <div class="row">
                        <div class="col-md-12 mt-1 mb-1">
                            <div class="form-actions clearfix">
                                <div class="float-left">
                                    <div class="content-header">Cancelled Tickets Page</div>
                                    <p class="content-sub-header">Total Cancelled Tickets: {{ $total_transactions }}</p>
                                </div>
                                <div class="float-right">
                                    <div class="my-4 pr-3">
                                        <a href="#" class="py-1 h6" data-toggle="modal" data-target="#cancelTicket"><i class="icon-docs font-medium-5 mr-2"></i>Cancel Ticket</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <section id="extended">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">List of Cancelled Transactions</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <table class="table table-responsive-md text-left">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Ticket No.</th>
                                                        <th>Outlet</th>
                                                        <th>Teller</th>
                                                        <th>Bets</th>
                                                        <th>Cancelled by</th>
                                                        <th>Cancelled Date</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($transactions as $transaction)
                                                    <tr>
                                                        <td>{{ $transaction->id(true) }}</td>
                                                        <td>{!! $transaction->tickets(true) !!}</td>
                                                        <td>{{ $transaction->outletName() }}</a></td>
                                                        <td>{{ $transaction->teller() }}</a></td>
                                                        <td>{{ $transaction->betsString() }}</td>
                                                        <td>{{ $transaction->canceledBy() }}</td>
                                                        <td>{{ $transaction->cancelDates() }}</td>
                                                        <td>
                                                            <a href="{{ route('single-transaction', ['transaction_id' => $transaction->getTransaction()->id, 'outlet_id' => $transaction->getOutlet()->id]) }}" class="info p-0" data-original-title="" title="">
                                                                <i class="ft-file-text font-large-1 mr-2"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="row" id="table-pagination">
                                                <div class="col-sm-12 col-md-5">
                                                    <div class="dataTables_info" id="DataTables_Table_0_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div>
                                                </div>
                                                <div class="col-sm-12 col-md-7">
                                                    <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
                                                        <ul class="pagination">
                                                            <li class="paginate_button page-item previous disabled"><a href="#"tabindex="0" class="page-link">Previous</a></li>
                                                            <li class="paginate_button page-item active"><a href="#" tabindex="0" class="page-link">1</a></li>
                                                            <li class="paginate_button page-item "><a href="#" tabindex="0" class="page-link">2</a></li>
                                                            <li class="paginate_button page-item "><a href="#" tabindex="0" class="page-link">3</a></li>
                                                            <li class="paginate_button page-item "><a href="#" tabindex="0" class="page-link">4</a></li>
                                                            <li class="paginate_button page-item "><a href="#" tabindex="0" class="page-link">5</a></li>
                                                            <li class="paginate_button page-item "><a href="#" tabindex="0" class="page-link">6</a></li>
                                                            <li class="paginate_button page-item next" id="DataTables_Table_0_next"><a href="#" tabindex="0" class="page-link">Next</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endsection
