@extends('layouts.main') @section('content')

                    <section id="settings">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title">System Settings</h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="card-block">

                                            @include('admin.settings.navbar')
                                            
                                            <div class="tab-content px-1">
                                                 <div role="tabpanel" class="tab-pane active" id="basic-settings-tab" aria-expanded="true" aria-labelledby="basic-settings-tab">
                                                    <p>Basic Settings Tab</p>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="tabBetReactivation" aria-expanded="true" aria-labelledby="tabBetReactivation1">
                                                    <div class="px-3">
                                                        <form class="form form-horizontal">
                                                            <div class="form-body">
                                                                <div class="form-group row">
                                                                    <label class="col-md-3 label-control" for="projectinput1">First Name: </label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" id="projectinput1" class="form-control" name="fname">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-actions">
                                                                <button type="button" class="btn btn-raised btn-warning mr-1">
                                                                    <i class="ft-x"></i> Cancel
                                                                </button>
                                                                <button type="button" class="btn btn-raised btn-primary">
                                                                    <i class="fa fa-check-square-o"></i> Save
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div role="tabpanel" class="tab-pane" id="tabVerticalLeft1" aria-expanded="true" aria-labelledby="baseVerticalLeft-tab1">
                                                    <div class="px-3">
                                                        <form class="form form-horizontal">
                                                            <div class="form-body">
                                                                <div class="form-group row">
                                                                    <label class="col-md-3 label-control" for="projectinput1">First Name: </label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" id="projectinput1" class="form-control" name="fname">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group row">
                                                                    <label class="col-md-3 label-control" for="projectinput2">Last Name: </label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" id="projectinput2" class="form-control" name="lname">
                                                                    </div>
                                                                </div>

                                                                <div class="form-group row">
                                                                    <label class="col-md-3 label-control" for="projectinput3">E-mail: </label>
                                                                    <div class="col-md-9">
                                                                        <input type="text" id="projectinput3" class="form-control" name="email">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="form-actions">
                                                                <button type="button" class="btn btn-raised btn-warning mr-1">
                                                                    <i class="ft-x"></i> Cancel
                                                                </button>
                                                                <button type="button" class="btn btn-raised btn-primary">
                                                                    <i class="fa fa-check-square-o"></i> Save
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                                <div class="tab-pane" id="tabVerticalLeft2" aria-labelledby="baseVerticalLeft-tab2">
                                                    <p>Sugar plum tootsie roll biscuit caramels. Liquorice brownie pastry cotton candy oat cake fruitcake jelly chupa chups. Pudding caramels pastry powder cake soufflé wafer caramels. Jelly-o pie cupcake.</p>
                                                </div>
                                                <div class="tab-pane" id="tabVerticalLeft3" aria-labelledby="baseVerticalLeft-tab3">
                                                    <p>Biscuit ice cream halvah candy canes bear claw ice cream cake chocolate bar donut. Toffee cotton candy liquorice. Oat cake lemon drops gingerbread dessert caramels. Sweet dessert jujubes powder sweet sesame snaps.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

<div class="content">
    <div id="e-commerce-product" class="page-layout simple tabbed">

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
