<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport"
	content="width=device width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">

	<title>Document</title>
	<style type="text/css">
		#content {
			margin-top: 10px;
			margin-bottom: 200px;
		}
		#contacts {
			float: left;
		}
		#unsubscribe {
			float: right;
			margin-right: 30px;
		}
		hr {
			margin-top: 20px;
		}
		footer {
			font-size: 0.8em;
			color: #555;
			margin-top: 10px;
		}
	</style>
</head>
<body>
	<div id="content">
		@yield('content')
	</div>
	
	<i>З повагою,<br>
	команда ТОЕсуд</i>
	
	<hr>
	<footer>
		<div id="contacts">
			ТОЕсуд<br>
			прозорий відкритий ефективний суд
		</div>
		
		<div id="unsubscribe">
			Даний лист відправлений системою <b>ТОЕсуд</b><br>
		</div>
	</footer>
</body>
</html>