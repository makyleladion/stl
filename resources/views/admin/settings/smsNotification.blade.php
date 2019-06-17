@extends('layouts.main') @section('content')

<div class="content">
    <div id="e-commerce-product" class="page-layout simple tabbed">

        <!-- HEADER -->
        <div class="page-header bg-secondary text-auto row no-gutters align-items-center justify-content-between p-6">

            <div class="row no-gutters align-items-center">

               <div class="logo-icon mr-3 mt-1">
                    <i class="icon-settings s-6"></i>
                </div>

                <div class="logo-text">
                    <div class="h4">SMS Notification</div>
                </div>

            </div>

        </div>
        <!-- / HEADER -->

        <!-- CONTENT -->
        <div class="page-content">

            @include('admin.settings.navbar')

            <div class="tab-content">

                <div class="tab-pane fade show active" id="sms-notification-tab-pane" role="tabpanel" aria-labelledby="sms-notification-tab">

                    <div class="card p-10">
                    
                    		@if (\Session::has('sms-form-success'))
                        <div class="alert alert-success" role="alert">{{ session('sms-form-success') }}</div>
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

                        <form action="{{ route('save-sms-notification') }}" method="post">
                        		{{ csrf_field() }}
                            <div class="form-group row">
                                <label for="smsNotification" class="col-sm-2 col-form-label" data-toggle="tooltip" data-placement="right" title="Set Time to Re-enable betting">Add Mobile No.</label>
                                <div class="col-sm-6">
                                    <input class="form-control" name="mobile_number" type="tel" value="63" id="smsNotification"/>
                                </div>
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-secondary btn-fab" title="Save"><i class="icon-plus"></i></button>
                                </div>
                            </div>

                            <div class="form-group row">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID No.</th>
                                            <th>Mobile Number</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    		@foreach($mobile_numbers as $m):
                                        <tr>
                                            <th scope="row">{{ $m->id }}</th>
                                            <td>{{ $m->mobile_number }}</td>
                                            <td><a href="{{ route('delete-mobile-number', ['mobile_id' => $m->id]) }}" class="btn btn-danger btn-fab"><i class="icon-trash"></i></a></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </form>
                    </div>

                </div>

            </div>

        </div>
        <!-- / CONTENT -->
    </div>


    <script type="text/javascript" src="{{url('/assets/js/apps/e-commerce/product/product.js?v=1')}}"></script>
</div>


@endsection
