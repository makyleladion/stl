@extends('layouts.app')

@section('content')

                <div id="login" class="p-8">

                    <div class="form-wrapper md-elevation-8 p-8">

                        <div class="logo bg-primary" style="display:none;">

                            <span></span>

                        </div>

                        <div class="title mt-4 mb-8">Log in to your account</div>

                        <form name="loginForm" method="post" action="{{ route('login') }}">

                          {{ csrf_field() }}

                          @if($errors->has('email') || $errors->has('password'))
                            @foreach ($errors->all() as $error)

                              <div class="alert alert-danger" role="alert">

                                <strong>Error!</strong> {{ $error }}

                              </div>

                            @endforeach
                          @endif

                          <div class="form-group mb-4">

                            <input type="email" name="email" class="form-control" id="loginFormInputUsername" placeholder="Email" value="{{ old('email') }}" required />

                            <label for="loginFormInputUsername">Email</label>

                          </div>

                          <div class="form-group mb-4">

                            <input type="password" name="password" class="form-control" id="loginFormInputPassword" placeholder="Password" required />

                            <label for="loginFormInputPassword">Password</label>

                          </div>

                          <div class="remember-forgot-password row no-gutters align-items-center justify-content-between pt-4">
                          </div>

                          <button type="submit" class="submit-button btn btn-block btn-primary my-4 mx-auto" aria-label="LOG IN">

                            LOG IN

                          </button>

                        </form>

                    </div>

                </div>

@endsection
