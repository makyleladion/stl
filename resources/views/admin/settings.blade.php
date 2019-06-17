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

                <div class="tab-pane fade show active" id="general-tab-pane" role="tabpanel" aria-labelledby="general-tab">

                    <div class="card p-10">

                        <form>

                            <div class="form-group row">
                                <label for="setBettingTime" class="col-sm-3 col-form-label" data-toggle="tooltip" data-placement="right" title="Set Time to Re-enable betting">Time to Re-enable betting</label>
                                <div class="col-sm-8">
                                    <input class="form-control" type="time" value="06:00:00" id="setBettingTime"/>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-secondary">Save Changes</button>

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
