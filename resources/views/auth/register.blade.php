@extends('layouts.app')

@section('content')

                    <div id="register-v2" class="row no-gutters">

                        <div class="intro col-12 col-md">

                            <div class="d-flex flex-column align-items-center align-items-md-start text-center text-md-left py-16 py-md-32 px-12">

                                <div class="logo bg-primary mb-8" style="width: 30.8rem;">
                                    <span>stl.ph</span>
                                </div>

                                <div class="title">
                                    Welcome to the Small Town Lottery!
                                </div>

                                <div class="description pt-2">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus ullamcorper nisl
                                    erat,
                                    vel convallis elit fermentum pellentesque. Sed mollis velit facilisis facilisis
                                    viverra.
                                </div>

                            </div>
                        </div>

                        <div class="form-wrapper col-12 col-md-auto d-flex justify-content-center p-4 p-md-0">

                            <div class="form-content md-elevation-8 h-100 bg-white text-auto py-16 py-md-32 px-12">

                                <div class="title h5">Create account</div>

                                <div class="description mt-2">Sed mollis velit facilisis facilisis viverra</div>

                                <form class="mt-8" method="POST" action="{{ route('register') }}">

                                    {{ csrf_field() }}

                                    <div class="form-group mb-4 {{ $errors->has('name') ? ' has-error' : '' }}">
                                        <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                                        <label for="name">Name</label>
                                        @if ($errors->has('name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group mb-4{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                                        <label for="email">Email address</label>

                                        @if ($errors->has('email'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group mb-4{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <input id="password" type="password" class="form-control" name="password" required>
                                        <label for="password">Password</label>

                                        @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group mb-4">
                                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                                        <label for="confirm-password">Password (Confirm)</label>
                                    </div>

                                    <div
                                        class="terms-conditions row align-items-center justify-content-center pt-4 mb-8">
                                        <div class="form-check mr-1 mb-1">
                                            <label class="form-check-label">
                                                <input type="checkbox" class="form-check-input"
                                                       aria-label="Remember Me"/>
                                                <span class="checkbox-icon"></span>
                                                <span>I read and accept</span>
                                            </label>
                                        </div>
                                        <a href="#" class="text-primary mb-1">terms and conditions</a>
                                    </div>

                                    <button type="submit" class="submit-button btn btn-block btn-primary my-4 mx-auto">
                                        CREATE MY ACCOUNT
                                    </button>

                                </form>

                                <div class="login d-flex flex-column flex-sm-row align-items-center justify-content-center mt-8 mb-6 mx-auto">
                                    <span class="text mr-sm-2">Already have an account?</span>
                                    <a class="link text-primary" href="{{ route('login') }}">Log in</a>
                                </div>

                            </div>
                        </div>
                    </div>

@endsection
