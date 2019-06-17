@extends('layouts.main') @section('content')
                    <section id="add-new-outlet">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="content-header">Add New Outlet</div>
                            </div>
                        </div>
                        <script>
                            $(function () {
                                $('#section2').hide();
                                $("#checkbox1").click(function () {
                                    if ($(this).is(":checked")) {
                                        $('#section1').hide();
                                        $('#section2').show();
                                    } else {
                                        $('#section1').show();
                                        $('#section2').hide();
                                    }
                                });
                            });
                        </script>
                        <div class="row">                            
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title" id="basic-layout-form"><i class="ft-user"></i> Outlet Information </h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="px-3">
                                            <form class="form">
                                                <div class="form-body">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-1">
                                                            <div class="custom-control custom-checkbox m-0">
                                                                <input type="checkbox" class="custom-control-input" id="checkbox1">
                                                                <label class="custom-control-label" for="checkbox1">Already have an account?</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <section id="section2">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="owner-email">Owner E-mail</label>
                                                                    <input type="text" id="email" class="form-control"  name="email">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="outlet-name">Outlet Name</label>
                                                                    <input type="text" id="outletname" class="form-control"  name="outletname">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="outlet-address">Address</label>
                                                                    <input type="text" id="address" class="form-control"  name="address">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </section>
                                                    <section id="section1">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="owner-name">Owner Name</label>
                                                                    <input type="text" id="ownername" class="form-control"  name="ownername">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="outlet-address">Address</label>
                                                                    <input type="text" id="address" class="form-control"  name="address">
                                                                </div>
                                                            </div>
                                                        </div>                                                        

                                                        <h4 class="form-section"><i class="ft-file-text"></i> Owner Information</h4>

                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="owner-email">Owner E-mail</label>
                                                                    <input type="text" id="email" class="form-control"  name="email">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="password">New Password</label>
                                                                    <input type="text" id="password" class="form-control"  name="password">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="confirm-password">Confirm New Password</label>
                                                                    <input type="text" id="confirm-password" class="form-control"  name="confirm-password">
                                                                </div>
                                                            </div>
                                                        </div> 
                                                    </section>   
                                                    <div class="row">
                                                        <div class="col-md-12 mt-1">
                                                            <div class="custom-control custom-checkbox m-0">
                                                                <input type="checkbox" class="custom-control-input" id="checkbox2">
                                                                <label class="custom-control-label" for="checkbox2">Is the new outlet affiliated to our Company?</label>
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
