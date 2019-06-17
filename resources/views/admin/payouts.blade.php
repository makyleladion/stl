@extends('layouts.main') 
@section('content')         
                    
                    <section id="payouts-page">
                        <div class="row">
                            <div class="col-md-12 mt-1 mb-1">
                                <div class="form-actions clearfix">
                                    <div class="float-left">
                                        <div class="content-header">Payouts Page</div>
                                        <p class="content-sub-header">Date: Today</p>
                                    </div>
                                    <div class="float-right">
                                        <div class="my-4 pr-3">
                                            <a href="#" class="py-1 mr-2 h6" data-toggle="modal" data-target="#filterPayouts"><i class="ft-search font-medium-5 mr-2"></i><span>Filter Payouts</span></a>
                                            <a href="#" class="py-1 h6" data-toggle="modal" data-target="#newPayout"><i class="icon-docs font-medium-5 mr-2"></i>New Payout</a>                                            
                                        </div>
                                    </div>
                                </div>                         
                            </div>
                        </div>

                        <div class="row" matchHeight="card">
                            <div class="col-xl-4 col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="px-3 py-3">
                                            <div class="media">
                                                <div class="media-body text-left">
                                                    <h3 class="mb-1 primary">&#8369;200,000</h3>
                                                    <span>Total Winners (200)</span>
                                                </div>
                                                <div class="media-right align-self-center">                                                    
                                                    <i class="ft-users primary font-large-2 float-right"></i>
                                                </div>
                                            </div>
                                            <div class="mt-2 mb-0">
                                                <ngb-progressbar type="primary" [value]="80" class="progress-bar-sm"></ngb-progressbar>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="px-3 py-3">
                                            <div class="media">
                                                <div class="media-body text-left">
                                                    <h3 class="mb-1 success">&#8369;200</h3>
                                                    <span>Paid (20)</span>
                                                </div>
                                                <div class="media-right align-self-center">
                                                    <i class="ft-user-check success font-large-2 float-right"></i>
                                                </div>
                                            </div>
                                            <div class="mt-2 mb-0">
                                                <ngb-progressbar type="success" [value]="60" class="progress-bar-sm"></ngb-progressbar>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-4 col-lg-6 col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="px-3 py-3">
                                            <div class="media">
                                                <div class="media-body text-left">
                                                    <h3 class="mb-1 danger">&#8369;423</h3>
                                                    <span>Unpaid (20)</span>
                                                </div>
                                                <div class="media-right align-self-center">
                                                    <i class="ft-file-text danger font-large-2 float-right"></i>
                                                </div>
                                            </div>
                                            <div class="mt-2 mb-0">
                                                <ngb-progressbar type="danger" [value]="40" class="progress-bar-sm"></ngb-progressbar>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">List of Paid Payouts</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <table class="table table-responsive-lg text-left">
                                                <thead>
                                                    <tr>
                                                        <th>Teller</th>
                                                        <th>Outlet</th>
                                                        <th>Ticket No.</th>
                                                        <th>Bet</span></th>
                                                        <th>Type</th>
                                                        <th>Amount</span></th>
                                                        <th>Winning Price</span></th>
                                                        <th>Draw Date</th>
                                                        <th>Payout Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($payouts as $payout)
                                                    <tr>
                                                        <td>{{ $payout->teller() }}</td>
                                                        <td>(STL-21) Moyco</td>
                                                        <td>{{ $payout->ticketNumber() }}</td>
                                                        <td>{{ $payout->bet()->betNumber() }}</td>
                                                        <td>{{ $payout->bet()->betType() }}</td>
                                                        <td>&#8369;{{ number_format($payout->bet()->amount(), 2, '.', ',') }}</td>
                                                        <td>&#8369;{{ number_format($payout->bet()->price(), 2, '.', ',') }}</td>
                                                        <td>{{ $payout->drawDateTime()->toDayDateTimeString() }}</td>
                                                        <td>{{ $payout->payoutDateTime()->toDayDateTimeString() }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">List of Unpaid Payouts</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <table class="table table-responsive-lg text-left">
                                                <thead>
                                                    <tr>
                                                        <th>Teller</th>
                                                        <th>Outlet</th>
                                                        <th>Ticket No.</th>
                                                        <th>Bet</span></th>
                                                        <th>Type</th>
                                                        <th>Amount</span></th>
                                                        <th>Winning Price</span></th>
                                                        <th>Draw Date</th>
                                                        <th>Payout Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($payouts as $payout)
                                                    <tr>
                                                        <td>{{ $payout->teller() }}</td>
                                                        <td>(STL-21) Moyco</td>
                                                        <td>{{ $payout->ticketNumber() }}</td>
                                                        <td>{{ $payout->bet()->betNumber() }}</td>
                                                        <td>{{ $payout->bet()->betType() }}</td>
                                                        <td>&#8369;{{ number_format($payout->bet()->amount(), 2, '.', ',') }}</td>
                                                        <td>&#8369;{{ number_format($payout->bet()->price(), 2, '.', ',') }}</td>
                                                        <td>{{ $payout->drawDateTime()->toDayDateTimeString() }}</td>
                                                        <td>{{ $payout->payoutDateTime()->toDayDateTimeString() }}</td>
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
