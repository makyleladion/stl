@extends('layouts.main') @section('content')

                    <section id="about">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="content-header">Edit User Profile</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title" id="basic-layout-form"><i class="ft-user"></i> Personal Information </h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="px-3">
                                            <form class="form" action="{{ route('update-user') }}" method="post">
                                                {{ csrf_field() }}
                                                <div class="form-body">
                                                    <div class="row">
                                                        <input type="hidden" name="user_id" value="{{ $user->id() }}" />
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="firstName">First Name</label>
                                                                <input type="text" name="firstName" id="firstName" class="form-control" aria-describedby="firstname" value="{{ $user->name() }}" />
                                                            </div>
                                                        </div>  
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="lastName">Last Name</label>
                                                                <input type="text" name="lastName" id="lastName" class="form-control" aria-describedby="lastname" />
                                                            </div>
                                                        </div> 
                                                        <div class="col-md-12">
                                                            @if ($user->role() == $user::ROLE_TELLER)
                                                            <div class="form-group">
                                                                <label for="outlet-assigned">Outlet Assigned</label>
                                                                <select name="outlet-assigned" id="outlet-assigned" class="form-control">
                                                                    <option value="0">Main Office</option>
                                                                    @foreach ($outlets as $outlet)
                                                                    <option value="{{ $outlet->id() }}">{{ $outlet->name() }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="role">Role</label>
                                                                <select id="role" name="role" class="form-control">
                                                                    <option value="{{ ucfirst($user->role()) }}" selected="" disabled="">{{ ucfirst($user->role()) }}</option>
                                                                    <option value="Coordinator">Coordinator</option>
                                                                    <option value="Teller">Teller</option>
                                                                    <option value="Usher ">Usher</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="percentage">Percentage</label>
                                                                <select id="percentage" name="percentage" class="form-control">
                                                                    <option value="7" selected="">7</option>
                                                                    <option value="10">10</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="select-coordinator">Select Coordinator</label>
                                                                <select id="select-coordinator" name="select-coordinator" class="form-control">
                                                                    <option value="1" selected="">1</option>
                                                                    <option value="2">2</option>
                                                                    <option value="3">3</option>
                                                                    <option value="4 ">5</option>
                                                                </select>
                                                            </div>
                                                        </div>                                                        
                                                    </div>
                                                </div>

                                                <div class="form-actions right">
                                                    <button type="button" class="btn btn-outline-primary round"><i class="fa fa-check mr-1"></i> Save Changes </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title" id="basic-layout-colored-form-control"><i class="ft-info"></i> User Account </h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="px-3">                      

                                            <form class="form">
                                                <div class="form-body">
                                                    <div class="row">
                                                      <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="username">Username</label>
                                                                <input type="text" name="username" id="username" class="form-control" aria-describedby="username" />
                                                            </div>
                                                        </div>        
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="email">E-mail</label>
                                                                <input type="email" name="email" id="email" class="form-control" aria-describedby="user email" value="{{ $user->email() }}" />
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="userinput1">Current Password</label>
                                                                <input type="password" id="userinput1" class="form-control"  name="name">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="userinput2">New Password</label>
                                                                <input type="password" id="userinput2" class="form-control"  name="company">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="userinput3">Confirm New Password</label>
                                                                <input type="password" id="userinput3" class="form-control"  name="username">
                                                            </div>
                                                        </div>
                                                    </div>    
                                                </div>

                                                <div class="form-actions right">
                                                    <button type="button" class="btn btn-outline-primary round"><i class="fa fa-check mr-1"></i> Save Changes </button>
                                                </div>
                                            </form>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

@endsection
