<!DOCTYPE html>
<html lang="zh-TW">
<head>
	<meta charset="UTF-8">
	<title>{{ $title }}</title>
</head>
<body>
	<ul>
		@foreach ($errList as $err)
			<li>{{ $err }}</li>
		@endforeach 
	</ul>
</body>
</html>