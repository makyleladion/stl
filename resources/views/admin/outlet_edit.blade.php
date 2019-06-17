@extends('layouts.main') @section('content')

<div class="page-layout carded full-width">

    <div class="top-bg bg-secondary"></div>

    <!-- CONTENT -->
    <div class="page-content">

        <!-- HEADER -->
        <div class="header bg-secondary text-auto row no-gutters align-items-center justify-content-between">

            <!-- APP TITLE -->
            <div class="col-12 col-sm">

                <div class="logo row no-gutters align-items-start">
                    <div class="logo-icon mr-3 mt-1">
                        <i class="icon-home-outline s-6"></i>
                    </div>
                    <div class="logo-text">
                        <div class="h4">Edit Outlet</div>
                    </div>
                </div>

            </div>
            <!-- / APP TITLE -->

        </div>
        <!-- / HEADER -->

        <div class="page-content-card">
            <div class="col-12 col-sm-6 col-xl-12 p-12">
                <div class="widget widget1 card p-6">

                    @if (\Session::has('outlet-success'))
                    <div class="alert alert-success" role="alert">{{ session('outlet-success') }}</div>
                    @endif @if (\Session::has('error-flash'))
                    <div class="alert alert-danger" role="alert">{{ session('error-flash') }}</div>
                    @endif @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div>
                        <form action="{{ route('update-outlet') }}" method="post">

                            {{ csrf_field() }}
                            {!! Form::hidden('outlet_id', $outlet->id); !!}

                            <div class="form-group">
                                <input type="text" name="outlet-name" class="form-control" aria-describedby="outlet name" value="{{ $outlet->name }}" />
                                <label>Outlet Name</label>
                            </div>

                            <div class="form-group">
                                <input type="text" name="address" class="form-control" aria-describedby="outlet tags" value="{{ $outlet->address }}" />
                                <label>Address</label>
                            </div>
                            <div class="form-group">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="is-24hours" value="1" class="custom-control-input">
                                    <span class="custom-control-indicator fuse-ripple-ready"></span>
                                    <span class="label">Is 24 hours?</span>
                                </label>
                            </div>
                            <div class="form-group">
                                <label class="custom-control custom-checkbox">
                                    <input type="checkbox" name="is-affiliated" value="1" class="custom-control-input"{{ ($outlet->is_affiliated) ? ' checked' : '' }}>
                                    <span class="custom-control-indicator fuse-ripple-ready"></span>
                                    <span class="label">Is the new outlet affiliated to our Company?</span>
                                </label>
                            </div>

                            <button type="submit" class="btn btn-secondary">SAVE</button>

                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- / CONTENT -->
</div>

<script type="text/javascript" src="{{url('/assets/js/apps/e-commerce/product/product.js?v=1')}}"></script>

@endsection
