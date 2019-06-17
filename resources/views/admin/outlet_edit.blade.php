@extends('layouts.main') @section('content')
                    <section id="edit-outlet">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="content-header">Edit Outlet</div>
                                <p class="content-sub-header">Outlet ID: {{ $outlet->id }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header">
                                        <h4 class="card-title" id="basic-layout-form"><i class="ft-user"></i> Outlet Information </h4>
                                    </div>
                                    <div class="card-body">
                                        <div class="px-3">
                                            <form class="form" action="{{ route('update-outlet') }}" method="post">
                                                {{ csrf_field() }}
                                                {!! Form::hidden('outlet_id', $outlet->id); !!}
                                                <div class="form-body">
                                                    <section id="section2">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="owner-name">Owner Name</label>
                                                                    <input type="text" id="owner-name" class="form-control"  name="owner-name" value="GGG">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="outlet-name">Outlet Name</label>
                                                                    <input type="text" id="outlet-name" class="form-control"  name="outlet-name" value="{{ $outlet->name }}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-12">
                                                                <div class="form-group">
                                                                    <label for="address">Address</label>
                                                                    <input type="text" id="address" class="form-control"  name="address" alue="{{ $outlet->address }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </section>                                                      
                                                    <div class="row">
                                                        <div class="col-md-12 mt-1">
                                                            <div class="custom-control custom-checkbox m-0">
                                                                <input type="checkbox" name="is-affiliated" value="1" class="custom-control-input" id="is-affiliated">
                                                                <label class="custom-control-label" for="is-affiliated">Is the new outlet affiliated to our Company?</label>
                                                            </div>
                                                        </div>
                                                    </div>                     
                                                </div>

                                                <div class="form-actions right">
                                                    <button type="submit" class="btn btn-outline-primary round"><i class="fa fa-check mr-1"></i> Save Changes </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
@endsection
