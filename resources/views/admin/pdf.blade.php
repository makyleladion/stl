

<style type="text/css">
div#pdf-wrapper .table-header, div#pdf-wrapper tr td, .pdf-table .table-header, .pdf-table tr td {
    padding: 10px 20px;
    text-align: left;
    font-family: 'Roboto', sans-serif;
        border: none!important;
}
.page-content-card.dashboard.pdf-table {
    margin-bottom: 30px;
}
.table thead th {
    border-bottom: 1px solid rgba(0,0,0,.12);
    font-weight: 500;
        padding: 0rem 0 2rem.8rem;
}
.pdf-table tbody tr:nth-of-type(odd) {
    background-color: rgba(12, 12, 12, 0.12);
}
.table {
    font-size: 9pt;
}
</style>
<div class="page-layout carded full-width">

    <div class="top-bg bg-secondary"></div>

    <!-- CONTENT -->
    <div class="page-content">



        <div class="page-content-card transaction pdf-table">

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
                                                        <span class="column-title">Bets</span>
                                                    </div>
                                                </th>

                                                <th>
                                                    <div class="table-header">
                                                        <span class="column-title">No. of Bets</span>
                                                    </div>
                                                </th>

                                                <th>
                                                    <div class="table-header">
                                                        <span class="column-title">Valid Amount</span>
                                                    </div>
                                                </th>

                                                <th>
                                                    <div class="table-header">
                                                        <span class="column-title">Date</span>
                                                    </div>
                                                </th>


                                            </tr>
                                        </thead>

                                    <tbody>
																				@foreach ($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id(true) }}</td>
                                            <td>{{ $transaction->transactionNumber() }}</td>
                                            <td>{!! $transaction->tickets(true) !!}</td>
                                            <td>{{ $transaction->outletName() }}</td>
                                            <td>{{ $transaction->teller() }}</td>
                                            <td>{{ $transaction->betsString() }}</td>
                                            <td>{{ $transaction->numberOfBets() }}</td>
                                            <td>PHP {{ number_format($transaction->amount(), 2, '.', ',') }}</td>
                                            <td>{{ $transaction->transactionDateTime()->toDayDateTimeString() }}</td>
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

