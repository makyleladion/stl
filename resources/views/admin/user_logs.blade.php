@extends('layouts.main') @section('content')

                    <section id="user-logs-page">
                        <div class="row">
                            <div class="col-md-12 mt-1 mb-1">
                                <div class="form-actions clearfix">
                                    <div class="float-left">
                                        <div class="content-header">User Logs</div>
                                        <p class="content-sub-header">User ID: 00157541 {{ $user->name }}</p>
                                    </div>
                                    <div class="float-right">
                                        <div class="my-4 pr-3">
                                            <a href="#" class="py-1 h6""><i class="ft-search font-medium-5 mr-2"></i>Filter Logs</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">User Activities</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <table class="table table-responsive-lg text-left">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>                                                        
                                                        <th>Device</th>
                                                        <th>User Agent</th>
                                                        <th>Action</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>11/12/2018</td>
                                                        <td>(Oppo A3s)</td>
                                                        <td>(App/Chrome)</td>
                                                        <td>Login</td>
                                                        <td>Description Here</td>
                                                    </tr>
                                                    @forelse ($logs as $log)
                                                    <tr>
                                                        <td>{{ Carbon\Carbon::parse($log['log_time'])->toDayDateTimeString() }}</td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>{{ $log['mode'] }}</td>
                                                        <td>{{ $log['description'] }}</td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="5"><p>No data has been found.</p></td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
@endsection
