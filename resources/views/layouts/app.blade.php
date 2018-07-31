<!DOCTYPE html>
<html lang="ua">
<head>
	<meta charset="utf-8">
	<meta name="description" content="ТОЕсуд - рейтинг суддів України що формується за допомогою алгоритмів ML на основі показників копетентності та своєчасності">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="ТОЕсуд, переатестація суддів, судді України" />
	<meta name="author" content="Andrii Poplavskiy" />
	
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSRF Token -->
	<meta name="csrf-token" content="{{ csrf_token() }}">

	<title>{{ config('app.name', 'ТОЕсуд') }}</title>

	<!-- Styles -->
	{{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> --}}
	<link href="{{ asset('css/plugins/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ asset('css/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
	<link href="{{ asset('css/style.css') }}" rel="stylesheet">
	<script src="{{ asset('js/plugins/jquery-3.3.1.min.js') }}"></script>

</head>

<noscript>
	<style type="text/css">
		.container {display: none;}
		h2 {color: red;}
	</style>
	<div align="center">
		<br/>
		<h2>для роботи сайтом необхідно увімкнути JavaScript у веб-переглядачі</h2>
		<a target="_blank" class="outer" href="https://support.google.com/adsense/answer/12654?hl=uk">ок Google як увімкнути JavaScript?</a></div>
</noscript>

<style type="text/css">
	#loader {
		border: 8px solid #f3f3f3;
		border-radius: 50%;
		border-top: 8px solid #2b989b;
		width: 80px;
		height: 80px;
		-webkit-animation: spin 1.5s linear infinite; /* Safari */
		animation: spin 1.5s linear infinite;
		z-index: 100;
		position:absolute;
		top: 50%;
		left: 45%;
	}
	.blurry {
		-webkit-filter: blur(5px);
		-moz-filter: blur(5px);
		-o-filter: blur(5px);
		-ms-filter: blur(5px);
		filter: blur(5px);
	}
	/* Safari */
	@-webkit-keyframes spin {
		0% { -webkit-transform: rotate(0deg); }
		100% { -webkit-transform: rotate(360deg); }
	}
	@keyframes spin {
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
	}
</style>

<body>

	<div id="app">
		<router-view></router-view>
	</div>
	<!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
	
	{{--<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>--}}
	<script src="{{ asset('js/plugins/bootstrap.min.js') }}"></script>
	<script src="{{ asset('js/main.js') }}"></script>
	{{--<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>--}}
</body>
</html>
