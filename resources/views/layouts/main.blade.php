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
    <style type="text/css">
        [fuse-cloak],
        .fuse-cloak {
            display: none !important;
        }
    </style>

    <link rel="icon" type="image/png" href="{{url('/favicon-16x16.png')}}" sizes="16x16" />

    <!-- Icons.css -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/icons/fuse-icon-font/style.css?v=1')}}">

    <!-- Animate.css -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/vendor/animate.css/animate.min.css?v=1')}}">

    <!-- PNotify -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/vendor/pnotify/pnotify.custom.min.css?v=1')}}">

    <!-- Nvd3 - D3 Charts -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/vendor/nvd3/build/nv.d3.min.css?v=1')}}" />

    <!-- Perfect Scrollbar -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/vendor/perfect-scrollbar/css/perfect-scrollbar.min.css?v=1')}}" />

    <!-- Fuse Html -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/vendor/fuse-html/fuse-html.min.css?v=1')}}" />

    <!-- Main CSS -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/css/main.css?v=1')}}">
    <!-- / STYLESHEETS -->

    <!-- Custom CSS -->
    <link type="text/css" rel="stylesheet" href="{{url('/assets/css/custom.css?v=3')}}">
    <!-- / STYLESHEETS -->
    
    <!-- Datepicker -->
    <link type="text/css" rel="stylesheet" href="{{url('/from_cdn/bootstrap-datepicker.standalone.min.css')}}">

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
    
    <!-- Datepicker -->
		<script type="text/javascript" src="{{url('/from_cdn/bootstrap-datepicker.min.js')}}"></script>

    <!-- Nvd3 - D3 Charts -->
    <script type="text/javascript" src="{{url('/assets/vendor/d3/d3.min.js?v=1')}}"></script>
    <script type="text/javascript" src="{{url('/assets/vendor/nvd3/build/nv.d3.min.js?v=1')}}"></script>

    <!-- PNotify -->
    <script type="text/javascript" src="{{url('/assets/vendor/pnotify/pnotify.custom.min.js?v=1')}}"></script>

    <!-- Fuse Html -->
    <script type="text/javascript" src="{{url('/assets/vendor/fuse-html/fuse-html.min.js?v=1')}}"></script>
		
    <!-- Main JS -->
    <script type="text/javascript" src="{{url('/assets/js/main.js?v=1')}}"></script>
    
    <script src="{{url('/js/common.js')}}"></script>

    <!-- / JAVASCRIPT -->

</head>

<body class="layout layout-vertical layout-left-navigation layout-below-toolbar" id="{{ (auth()->user()->is_admin) ? 'is-admin' : 'is-teller' }}">


    <div id="wrapper">  
        
        @if (auth()->user()->is_admin)
          @include('inc.aside-left')
        @endif

        <div class="content-wrapper">

            @include('inc.header-nav')

            <div class="content">

                @yield('content')  
                
            </div>

        </div>  

    </div>


<script src="{{url('/assets/js/moment.js')}}"></script>
<script type="text/javascript">
    function refreshAt(hours, minutes, seconds) {
        var now = new Date();
        var then = new Date();
    
        if(now.getHours() > hours ||
           (now.getHours() == hours && now.getMinutes() > minutes) ||
            now.getHours() == hours && now.getMinutes() == minutes && now.getSeconds() >= seconds) {
            then.setDate(now.getDate() + 1);
        }
        then.setHours(hours);
        then.setMinutes(minutes);
        then.setSeconds(seconds);
    
        var timeout = (then.getTime() - now.getTime());
        setTimeout(function() { window.location.reload(true); }, timeout);
    }
    
    refreshAt(10,44,0)
    refreshAt(10,45,0)
    
    refreshAt(15,44,0)
    refreshAt(15,45,0)
    
    refreshAt(20,44,0)
    refreshAt(20,45,0)
</script>
<script type="text/javascript">
$(document).ready(function() {
	var isSubmit = false;
	$('form').on('submit', function(e) {
		if (!isSubmit) {
			isSubmit = true;
		} else {
			e.preventDefault();
		}
	});
});
</script>
</body>

</html>
