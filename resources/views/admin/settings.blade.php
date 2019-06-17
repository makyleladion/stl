@extends('layouts.main') @section('content')

                    <section id="settings-page">
                        <div class="row">
                            <div class="col-md-12 mt-1 mb-1">
                                <div class="content-header">Settings Page</div>
                                <p class="content-sub-header"></p>                    
                            </div>
                        </div>
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
                                                    <div class="px-3 pt-2">
                                                        <form class="form form-horizontal">
                                                            <div class="form-body">
                                                                <div class="form-group row">
                                                                    <label class="col-md-4 label-control" for="setBettingTime">Time to Re-enable betting: </label>
                                                                    <div class="col-md-8">
                                                                        <input type="text" id="setBettingTime" class="form-control" type="time" value="06:00:00" name="setBettingTime">
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
                                </div>
                            </div>
                        </div>
                    </section>  

@endsection
