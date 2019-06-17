@extends('layouts.main') 
@section('content')
                    
                    <section id="users-page">
                        <div class="row">
                            <div class="col-md-12 mt-1 mb-1">
                                <div class="form-actions clearfix">
                                    <div class="float-left">
                                        <div class="content-header">System Users</div>
                                        <p class="content-sub-header">Total Users: {{ $total_users }}</p>
                                    </div>
                                    <div class="float-right">
                                        <div class="my-4 pr-3">
                                            <a href="{{route('new-user')}}" class="py-1 h6"><i class="icon-user-follow font-medium-5 mr-2"></i>New User</a>
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
                                            <div class="float-left"><h4 class="card-title">List of Users</h4></div>
                                            <div class="float-right"><input type="text" class="form-control" id="basicInput" placeholder="Search by Name"></div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">
                                            <table class="table table-responsive-lg text-left">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Role</th>
                                                        <th>Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($users as $user)
                                                    <tr>
                                                        <td>{{ $user->id(true) }}</td>
                                                        <td>{{ $user->name() }}</td>
                                                        <td>{{ $user->email() }}</td>
                                                        <td>{{ ucfirst($user->role()) }}</td>
                                                        <td>
                                                            <a href="{{ route('view-user-log', ['user_id' => $user->id()]) }}" class="btn btn-flat btn-success m-0 px-1" data-toggle="tooltip" data-placement="top" title="View User Log" data-trigger="hover"> <i class="ft-file-text font-medium-3"></i></a>
                                                            @if (!auth()->user()->is_read_only)
                                                            <a href="{{ route('edit-user', ['user_id' => $user->id()]) }}" class="btn btn-flat btn-primary m-0 px-1" data-toggle="tooltip" data-placement="top" title="Edit User" data-trigger="hover"> <i class="ft-edit font-medium-3"></i></a>
                                                            @endif
                                                            @if (auth()->user()->id != $user->id())
                                                            <a href="#" id="confirm-delete-user" class="btn btn-flat btn-danger m-0 px-1" data-toggle="tooltip" data-placement="top" title="Remove User" data-trigger="hover"> <i class="ft-trash-2 font-medium-3"></i></a>
                                                             @endif
                                                            <input type="checkbox" data-toggle="tooltip" data-placement="top" title="Disable Outlet/Enable Outlet" data-trigger="hover" id="enable-disable" class="switchery outlet_status_toggle" data-size="sm" checked/>
                                                        </td>
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
