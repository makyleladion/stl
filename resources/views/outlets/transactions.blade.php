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
                        <i class="icon-cart-outline s-6"></i>
                    </div>

                    <div class="logo-text">
                        <div class="h4">Transactions</div>
                        <div class="">Total Transactions: 0</div>
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

            <table id="e-commerce-orders-table" class="table dataTable">

                <thead>

                    <tr>

                        <th>
                            <div class="table-header">
                                <span class="column-title">ID</span></div>
                        </th>
                        <th>
                            <div class="table-header">
                                <span class="column-title">Game</span>
                            </div>
                        </th>
                        <th>
                            <div class="table-header">
                                <span class="column-title">Teller Name</span>
                            </div>
                        </th>
                        <th>
                            <div class="table-header">
                                <span class="column-title">No. of Bets</span>
                            </div>
                        </th>

                        <th>
                            <div class="table-header">
                                <span class="column-title">Amount (PHP 0.00)</span>
                            </div>
                        </th>

                        <th>
                            <div class="table-header">
                                <span class="column-title">Ticket ID</span>
                            </div>
                        </th>

                        <th>
                            <div class="table-header">
                                <span class="column-title">Date</span>
                            </div>
                        </th>

                        <th>
                            <div class="table-header">
                                <span class="column-title">Action</span>
                            </div>
                        </th>

                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
    <!-- / CONTENT -->
</div>

@endsection
