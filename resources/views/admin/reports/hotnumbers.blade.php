@extends('layouts.main') @section('content')

                    <section id="highestBetReports">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-actions clearfix">
                                    <div class="float-left">
                                        <div class="content-header">Hot Numbers Page</div>                                
                                        <p class="content-sub-header">Date: Today</p>  
                                    </div>
                                    <div class="float-right">
                                        <div class="my-4 pr-3">
                                            <a href="#" class="py-1 h6" data-toggle="modal" data-target="#hotNumbers"><i class="ft-search font-medium-5 mr-2"></i><span>Filter Hot Numbers</span></a>
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
                                            <div class="float-left"><h4 class="card-title">List of Hot Numbers</h4></div>
                                            <div class="float-right"><input type="text" class="form-control" id="basicInput" placeholder="Search by Hot Numbers"></div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <table class="table table-responsive-lg text-left">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Bet Number</th>
                                                        <th>Total Amount</th>
                                                        <th>Bet Count</span></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>110</td>
                                                        <td>&#8369;795.00</td>
                                                        <td>92</td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>142</td>
                                                        <td>&#8369;795.00</td>
                                                        <td>92</td>
                                                    </tr>  
                                                    <tr>
                                                        <td>3</td>
                                                        <td>564</td>
                                                        <td>&#8369;795.00</td>
                                                        <td>92</td>
                                                    </tr>  
                                                    <tr>
                                                        <td>4</td>
                                                        <td>789</td>
                                                        <td>&#8369;795.00</td>
                                                        <td>92</td>
                                                    </tr>                                              
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

@endsection
