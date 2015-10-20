<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<meta charset="UTF-8">
	<title>{{ $title or '首頁'}}</title>
	<link rel="stylesheet" href="https://bootswatch.com/united/bootstrap.min.css">
	<link rel="stylesheet" href="{!! URL::asset('/assets/css/timeline.css') !!}">
	
  @yield('css')
</head>

<body>
	@include('common/header')
	
	<div class="container">
		@include('common/pageHeader')

		@section('body')				
			@include('basic/timeline')
		@show
	
		@include('common/footer')	
	</div>

	<script src="https://bootswatch.com/bower_components/jquery/dist/jquery.min.js"></script>
	<script src="https://bootswatch.com/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
	
	@yield('js')
</body>
</html>