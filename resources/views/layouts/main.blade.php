<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="apple-touch-icon" sizes="60x60" href="{{url('/assets/img/ico/apple-icon-60.html')}}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{url('/assets/img/ico/apple-icon-76.html')}}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{url('/assets/img/ico/apple-icon-120.html')}}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{url('/assets/img/ico/apple-icon-152.html')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{url('/assets/img/smalltownlottery.png')}}">
    <link rel="shortcut icon" type="image/png" href="{{url('/assets/img/smalltownlottery.png')}}">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-touch-fullscreen" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500,700,900|Montserrat:300,400,500,600,700,800,900" rel="stylesheet">
    <!-- BEGIN VENDOR CSS-->
    <!-- font icons-->
    <link rel="stylesheet" type="text/css" href="{{url('/assets/fonts/feather/style.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/assets/fonts/simple-line-icons/style.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/assets/fonts/font-awesome/css/font-awesome.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/assets/vendors/css/perfect-scrollbar.min.css')}}">    
    <link rel="stylesheet" type="text/css" href="{{url('/assets/vendors/css/prism.min.css')}}">     
    <link rel="stylesheet" type="text/css" href="{{url('/assets/vendors/css/switchery.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/assets/vendors/css/pickadate/pickadate.css')}}">  
    <link rel="stylesheet" type="text/css" href="{{url('/assets/vendors/css/sweetalert2.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{url('/assets/vendors/css/toastr.css')}}">
    <!-- END VENDOR CSS-->
    <!-- BEGIN CSS-->
    <link rel="stylesheet" type="text/css" href="{{url('/assets/css/app.css')}}"> 
    <link rel="stylesheet" type="text/css" href="{{url('/assets/css/custom.css')}}">   
    <!-- END CSS-->

    
</head>
<body data-col="2-columns" class=" 2-columns" id="{{ (auth()->user()->is_admin) ? 'is-admin' : 'is-teller' }}">
    
    <div class="wrapper">          
        @include('inc.sidebar')
        @include('inc.navbar')
        @include('inc.modals')        
        <div class="main-panel">
            <div class="main-content">
                <div class="content-wrapper">  
                    @yield('content')
                </div>
            </div>     
            @include('inc.footer')       
        </div>        
    </div>

    @include('inc.notification-sidebar')
    <!-- BEGIN VENDOR JS-->
    <script src="{{url('/assets/vendors/js/core/jquery-3.2.1.min.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/vendors/js/core/popper.min.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/vendors/js/core/bootstrap.min.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/vendors/js/perfect-scrollbar.jquery.min.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/vendors/js/prism.min.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/vendors/js/jquery.matchHeight-min.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/vendors/js/screenfull.min.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/vendors/js/pace/pace.min.js')}}" type="text/javascript"></script>
    <!-- BEGIN VENDOR JS-->
    <!-- BEGIN PAGE VENDOR JS-->   
    <script src="{{url('/assets/vendors/js/switchery.min.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/vendors/js/pickadate/picker.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/vendors/js/pickadate/picker.date.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/vendors/js/pickadate/picker.time.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/vendors/js/pickadate/legacy.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/vendors/js/sweetalert2.min.js')}}" type="text/javascript"></script>
    <!-- END PAGE VENDOR JS-->
    <!-- BEGIN JS-->
    <script src="{{url('/assets/js/app-sidebar.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/js/notification-sidebar.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/js/customizer.js')}}" type="text/javascript"></script>
    <!-- END JS-->
    <!-- BEGIN PAGE LEVEL JS-->
    <script src="{{url('/assets/js/switch.min.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/js/pick-a-datetime.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/js/tooltip.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/js/sweet-alerts.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/js/toastr.min.js')}}" type="text/javascript"></script>
    <script src="{{url('/assets/js/custom.js')}}" type="text/javascript"></script>    
    <!-- END PAGE LEVEL JS-->    
</body>
</html>
