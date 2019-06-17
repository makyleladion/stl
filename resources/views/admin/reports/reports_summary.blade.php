@extends('layouts.main') @section('content')

                    <section id="summaryReports">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-actions clearfix">
                                    <div class="float-left">
                                        <div class="content-header">Summary Reports Page</div>                                
                                            @if (date('Y-m-d') === $draw_date)
                                            <p class="content-sub-header">Date: Today</p>
                                            @else
                                            <p class="content-sub-header">Date: {{ date('l, F j, Y', strtotime($draw_date)) }}</p>      
                                            @endif  
                                    </div>
                                    <div class="float-right">
                                        <div class="my-4 pr-3">
                                            <a href="#" class="py-1 h6" data-toggle="modal" data-target="#summaryReports"><i class="ft-search font-medium-5 mr-2"></i><span>Filter Summary Reports</span></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <div class="form-actions clearfix">
                                            <div class="float-left"><h4 class="card-title">Total Sales per Outlet</h4></div>
                                            <div class="float-right"><input type="text" class="form-control" id="basicInput" placeholder="Search by Outlet Name"></div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <table class="table table-responsive-md text-left">
                                                @if (!$is_usher)
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Outlet</th>
                                                        <th>Teller</th>
                                                        @foreach (\App\System\Data\Timeslot::drawTimeslots() as $schedKey => $time)
                                                        <th>{{ date('g:ia', strtotime($time)) }}</th>
                                                        @endforeach
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $rank = 1; ?>
                                                    <?php $ids = []; ?>
                                                    @foreach($outlets_summary as $summary)
                                                      <?php $ids[] = $summary['outlet']->id(); ?>
                                                      <tr>
                                                          <td>{{ $rank++ }}</td>                          
                                                          <td>{{ $summary['outlet']->name() }}</td>
                                                          <td>{{ $summary['outlet']->assignedTellers(true) }}</td>
                                                          @foreach (\App\System\Data\Timeslot::drawTimeslots() as $schedKey => $time)
                                                          <td>&#8369;{{ number_format($summary['sales'][$schedKey], 0, '.', ',') }}</td>
                                                          @endforeach
                                                          <td>&#8369;{{ number_format($summary['sales']['total'], 0, '.', ',') }}</td>
                                                      </tr>
                                                    @endforeach      
                                                </tbody>
                                                @else
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Outlet</th>
                                                        <th>Teller</th>
                                                        @foreach (\App\System\Data\Timeslot::drawTimeslots() as $schedKey => $time)
                                                        <th>{{ date('g:ia', strtotime($time)) }}</th>
                                                        @endforeach
                                                        <th>Gross Sales</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php $rank = 1; ?>
                                                    <?php $ids = []; ?>
                                                    @foreach($users_summary as $summary)
                                                      <?php $ids[] = $summary['user']->id(); ?>
                                                      <tr>
                                                          <td>{{ $rank++ }}</td>                          
                                                          <td>{{ $summary['user']->name() }}</td>
                                                          @foreach (\App\System\Data\Timeslot::drawTimeslots() as $schedKey => $time)
                                                          <td>PHP {{ number_format($summary['sales'][$schedKey], 0, '.', ',') }}</td>
                                                          @endforeach
                                                          <td>
                                                            <b>PHP {{ number_format($summary['sales']['total'], 0, '.', ',') }}</b>
                                                          </td>
                                                      </tr>
                                                    @endforeach      
                                                </tbody>
                                                @endif
                                                <tfoot>
                                                    <tr>
                                                        <th scope="row" colspan="3">Total</th>
                                                        @foreach (\App\System\Data\Timeslot::drawTimeslots() as $schedKey => $time)
                                                        <td><b>&#8369;{{ number_format($totals[$schedKey], 0, '.', ',') }}</b></td>
                                                        @endforeach
                                                        <td><b>&#8369;{{ number_format($totals['overall'], 0, '.', ',') }}</b></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

@endsection
