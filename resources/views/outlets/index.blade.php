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
                        <i class="icon-cube-outline s-6"></i>
                    </div>
                    <div class="logo-text">
                        <div class="h4">Select Outlet Location</div>
                    </div>
                </div>

            </div>
            <!-- / APP TITLE -->

        </div>
        <!-- / HEADER -->

        <div class="page-content-card">

            <table id="e-commerce-products-table" class="table dataTable">

                <thead>

                    <tr>

                        <th>
                            <div class="table-header">
                                <span class="column-title">Outlet Name</span>
                            </div>
                        </th>

                        <th>
                            <div class="table-header">
                                <span class="column-title">Address</span>
                            </div>
                        </th>

                        <th>
                            <div class="table-header">
                                <span class="column-title">Action</span>
                            </div>
                        </th>

                    </tr>
                </thead>

                <tbody>

                    <tbody>
                        @foreach($outlets as $outlet)
                        <tr>
                            <td><a href="{{ route('dashboard', ['outlet_id' => $outlet->id]) }}">{{ $outlet->name }}</a></td>
                            <td>{{ $outlet->address }}</td>
                            <td><a href="{{ route('dashboard', ['outlet_id' => $outlet->id]) }}" class="btn btn-block btn-success">Proceed</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </tbody>
            </table>
        </div>
    </div>
    <!-- / CONTENT -->
</div>

@endsection
