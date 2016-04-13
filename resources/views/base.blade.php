<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<meta charset="utf-8">
	<title>@section('title'){{ $title or '首頁'}}@show</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="author" content="Jocoonopa">
	<meta name="description" content="Lubri - For Nutrimate Emps, much more easy to handle data and tasks">
	<meta name="keywords" content="nutrimate, chinghwa, IT, lubri, flap">
	{{-- <link rel="stylesheet" href="https://bootswatch.com/united/bootstrap.min.css"> --}}
	<!-- Bootstrap -->
  	<link rel="stylesheet" href="{!! URL::asset('/assets/css/bootstrap.min.css') !!}" >
  	<link rel="stylesheet" href="{!! URL::asset('/assets/css/bootstrap.override.css') !!}">
  	<link rel="stylesheet" href="{!! URL::asset('/assets/css/bootstrap.extend.css') !!}">
	<link rel="stylesheet" href="{!! URL::asset('/assets/css/timeline.css') !!}">
	<link rel="shortcut icon" type="image/png" href="{!! URL::asset('/assets/image/favicon.png') !!}"/>
	<link rel="apple-touch-icon" href="{!! URL::asset('/assets/image/favicon.png') !!}">

	<!-- Material Design fonts -->
  	<link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Roboto:300,400,500,700" type="text/css">
  	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<link rel="stylesheet" href="{!! URL::asset('/assets/css/bootstrap-material-design.min.css') !!}">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-material-design/0.5.6/css/bootstrap-material-design.min.css.map">
	<link rel="stylesheet" href="{!! URL::asset('/assets/css/ripple.min.css') !!}">
	<link rel='stylesheet' href='{!! URL::asset('/assets/nprogress-master/nprogress-master/nprogress.css') !!}'/>
  @yield('css')
</head>

<body id="body">
	@include('common/header')
	
	<div class="container">
		@include('common/pageHeader')

		@section('body')				
			@include('basic/timeline')
		@show
	
		@include('common/footer')	
	</div>

	<script src="{!! URL::asset('/assets/js/jquery.min.js') !!}"></script>
	<script src="{!! URL::asset('/assets/js/bootstrap.min.js') !!}"></script>
	<script src="{!! URL::asset('/assets/js/bootbox.min.js') !!}"></script>
	<script src="{!! URL::asset('/assets/js/helper.js') !!}"></script>
	<script src="{!! URL::asset('/assets/js/material.min.js') !!}"></script>
	<script src="{!! URL::asset('/assets/js/ripple.min.js') !!}"></script>
	<script src='{!! URL::asset('/assets/nprogress-master/nprogress-master/nprogress.js') !!}'></script>
	<script>
		$.material.init();
		NProgress.start();
    	setTimeout(function() { NProgress.done(); }, Math.floor(Math.random() * 2) + 1);
	</script>
	@yield('js')
</body>
</html>