@extends('layouts.main') @section('content')

                    <section id="highestBetReports">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-actions clearfix">
                                    <div class="float-left">
                                        <div class="content-header">Highest Bet Page</div>    
                                        @if (date('Y-m-d') === $draw_date)
                                        <p class="content-sub-header">Date: Today</p>      
                                        @else
                                        <p class="content-sub-header">Date: {{ date('l, F j, Y', strtotime($draw_date)) }}</p>      
                                        @endif         
                                    </div>
                                    <div class="float-right">
                                        <div class="my-4 pr-3">
                                            <a href="#" class="py-1 h6" data-toggle="modal" data-target="#highestBet"><i class="ft-search font-medium-5 mr-2"></i><span>Filter Highest Bet</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title" id="basic-layout-form">List of all Highest Bet</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <table class="table table-responsive-lg text-left">
                                                <thead>
                                                    <tr>
                                                        <th>Ticket No</th>
                                                        <th>Outlet</th>
                                                        <th>Bet</th>
                                                        <th>Game</span></th>
                                                        <th>Type</th>
                                                        <th>Amount</th>
                                                        <th>Win</th>
                                                        <th>Draw Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($highest_winnings as $hwinner)
                                                    <tr>
                                                        <td>{{ $hwinner->ticketNumber() }}</td>
                                                        <td>{{ $hwinner->outletName() }}</td>
                                                        <td>{{ $hwinner->bet() }}</td>
                                                        <td>{{ $hwinner->game() }}</td>
                                                        <td>{{ $hwinner->type() }}</td>
                                                        <td>&#8369;{{ number_format($hwinner->amount(), 2, '.', ',') }}</td>
                                                        <td>&#8369;{{ number_format($hwinner->winningPrize(), 2, '.', ',') }}</td>
                                                        <td>{{ $hwinner->drawDateTimeCarbon()->toDayDateTimeString() }}</td>
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

@endsection
