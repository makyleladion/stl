<!DOCTYPE html>

<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- STYLESHEETS -->

    <link rel="icon" type="image/png" href="{{url('/favicon-16x16.png')}}" sizes="16x16" />

    <!-- Icons.css -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/icons/fuse-icon-font/style.css?v=1')}}">

    <!-- Animate.css -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/vendor/animate.css/animate.min.css?v=1')}}">

    <!-- PNotify -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/vendor/pnotify/pnotify.custom.min.css?v=1')}}">

    <!-- Nvd3 - D3 Charts -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/vendor/nvd3/build/nv.d3.min.css?v=1')}}"/>

    <!-- Perfect Scrollbar -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/vendor/perfect-scrollbar/css/perfect-scrollbar.min.css?v=1')}}"/>

    <!-- Fuse Html -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/vendor/fuse-html/fuse-html.min.css?v=1')}}"/>

    <!-- Main CSS -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/css/main.css?v=1')}}">
    <!-- Custom Css -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/css/custom.css?v=1')}}">
    <!-- / STYLESHEETS -->

    <!-- JAVASCRIPT -->

    <!-- jQuery -->
    <script type="text/javascript" src="{{url('/assets/vendor/jquery/dist/jquery.min.js?v=1')}}"></script>


    <!-- Mobile Detect -->
    <script type="text/javascript" src="{{url('/assets/vendor/mobile-detect/mobile-detect.min.js?v=1')}}"></script>

    <!-- Perfect Scrollbar -->
    <script type="text/javascript" src="{{url('/assets/vendor/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js?v=1')}}"></script>

    <!-- Popper.js -->
    <script type="text/javascript" src="{{url('/assets/vendor/popper.js/index.js?v=1')}}"></script>

    <!-- Bootstrap -->
    <script type="text/javascript" src="{{url('/assets/vendor/bootstrap/bootstrap.min.js?v=1')}}"></script>

    <!-- Nvd3 - D3 Charts -->
    <script type="text/javascript" src="{{url('/assets/vendor/d3/d3.min.js?v=1')}}"></script>
    <script type="text/javascript" src="{{url('/assets/vendor/nvd3/build/nv.d3.min.js?v=1')}}"></script>

    <!-- Data tables -->
    <script type="text/javascript" src="{{url('/assets/vendor/datatables.net/js/jquery.dataTables.min.js?v=1')}}"></script>

    <script type="text/javascript" src="{{url('/assets/vendor/datatables-responsive/js/dataTables.responsive.js?v=1')}}"></script>

    <!-- PNotify -->
    <script type="text/javascript" src="{{url('/assets/vendor/pnotify/pnotify.custom.min.js?v=1')}}"></script>

    <!-- Fuse Html -->
    <script type="text/javascript" src="{{url('/assets/vendor/fuse-html/fuse-html.min.js?v=1')}}"></script>

    <!-- Main JS -->
    <script type="text/javascript" src="{{url('/assets/js/main.js?v=1')}}"></script>

    <!-- / JAVASCRIPT -->

    <style type="text/css">
        .layout.layout-vertical.layout-above-toolbar #wrapper>.content-wrapper>.content { margin-top: 0px; }
    </style>

</head>

<body class="layout layout-vertical layout-left-navigation layout-above-toolbar">

    

    <div id="wrapper">  

        <div class="content-wrapper">

          @include('inc.header-nav')

            <div class="content">

                @yield('content')

            </div>

        </div>        

    </div>

</body>

</html>
