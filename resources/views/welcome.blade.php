@extends('layouts.app')

@section('content')

                <div id="register-v2" class="row no-gutters">

                    <div class="intro col-12 col-md">

                        <div class="d-flex flex-column align-items-center align-items-md-start text-center text-md-left py-16 py-md-32 px-12">

                            <div class="logo bg-primary mb-8" style="width: 30.8rem;">
                                <span>stl.ph</span>
                            </div>

                            <div class="title"> Welcome to the Small Town Lottery! </div>

                            <div class="description pt-2">                                
                                @guest
                                  <span class="text mr-sm-2">Already have an account?</span>
                                  <a class="link text-primary" href="{{ route('login') }}">Log in</a>
                                @else
                                  <span class="text mr-sm-2">You're currently logged in!</span>
                                  <a class="link text-primary" href="{{ route('login') }}">Go to dashboard</a>
                                @endguest
                            </div>

                        </div>

                    </div>
                        
                </div>
                
@endsection
