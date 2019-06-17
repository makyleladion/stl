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
                        <i class="icon-message-text s-6"></i>
                    </div>
                    <div class="logo-text">
                        <div class="h4">New Memo</div>
                    </div>
                </div>

            </div>
            <!-- / APP TITLE -->

        </div>
        <!-- / HEADER -->

        <div class="page-content-card">
            <div class="col-12 col-sm-6 col-xl-12 p-12">
                <div class="widget widget1 card p-6">

                    <div id="div2">
                        <form action="{{ route('create-memo') }}" method="post">

                            {{ csrf_field() }}


                            <div class="form-group">
                                <input type="text" name="message" class="form-control" aria-describedby="message tags" />
                                <label>Message</label>
                            </div>

                            <button type="submit" class="btn btn-secondary">SEND</button>

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
