<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<meta charset="UTF-8">
	<title>{{ $title }}</title>
</head>
<body style="font-family: 微軟正黑體, Roboto,Helvetica,Arial,sans-serif; font-weight: bold;">
	<ul>
		@foreach ($errList as $err)
			<li>{{ $err }}</li>
		@endforeach 
	</ul>
</body>
</html>