@extends('layouts.app') 

@section('content')

        <div class="main-panel">
            <div class="main-content">
                <div class="content-wrapper">
                    <!--Login Page Starts-->
                    <section id="login">
                        <div class="container-fluid">
                            <div class="row full-height-vh">
                                <div class="col-12 d-flex align-items-center justify-content-center">
                                    <div class="card gradient-indigo-purple text-center width-400">
                                        <div class="card-img overlap">
                                            <img alt="element 06" class="mb-1" src="assets/img/portrait/avatars/avatar-08.png" width="190">
                                        </div>
                                        <div class="card-body">
                                            <div class="card-block">
                                                <h2 class="white">Login</h2>

                                                <form name="loginForm" method="post" action="{{ route('login') }}">

                                                    {{ csrf_field() }} @if($errors->has('email') || $errors->has('password')) @foreach ($errors->all() as $error)

                                                    <div class="alert alert-danger" role="alert">

                                                        <strong>Error!</strong> {{ $error }}

                                                    </div>

                                                    @endforeach @endif

                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <input type="email" name="email" class="form-control" id="loginFormInputUsername" placeholder="Email" value="{{ old('email') }}" required />
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <input type="password" name="password" class="form-control" id="loginFormInputPassword" placeholder="Password" required />
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="custom-control custom-checkbox mb-2 mr-sm-2 mb-sm-0 ml-3">
                                                                    <input type="checkbox" class="custom-control-input" checked id="rememberme">
                                                                    <label class="custom-control-label float-left white" for="rememberme">Remember Me</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <div class="col-md-12">
                                                            <button type="submit" class="submit-button btn btn-block btn-primary my-4 mx-auto" aria-label="LOG IN">LOG IN</button>
                                                        </div>
                                                    </div>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                    <!--Login Page Ends-->
                </div>
            </div>
        </div>

@endsection