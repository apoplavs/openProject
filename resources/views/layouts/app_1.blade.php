<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
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
	{{--<link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}
	{{-- <link href="{{ asset('css/plugins/bootstrap.min.css') }}" rel="stylesheet"> --}}
	<link href="{{ asset('css/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
	<link href="{{ asset('css/style.css') }}" rel="stylesheet">
	{{-- <script src="{{ asset('js/plugins/jquery-3.3.1.min.js') }}"></script> --}}
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
<body>
	<div id="app">
		<div class="panel-heading">Companies</div>
 
<div class="panel-body table-responsive">
  <router-view></router-view>
</div>
	</div>	


	<!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
	
{{--	<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>--}}
	{{-- <script src="{{ asset('js/plugins/bootstrap.min.js') }}"></script>
	<script src="{{ asset('js/main.js') }}"></script> --}}
	{{--<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>--}}
</body>
</html>
