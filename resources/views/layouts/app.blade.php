<!DOCTYPE html>
<html lang="ua">
<head>
	<meta charset="utf-8">
	<meta name="description" content="ТОЕсуд - рейтинг суддів України що формується за допомогою алгоритмів ML на основі показників копетентності та своєчасності">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="ТОЕсуд, переатестація суддів, судді України" />
	<meta name="author" content="Andrii Poplavskiy" />
	
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>{{ config('app.name', 'ТОЕсуд') }}</title>

	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.2/css/all.css" integrity="sha384-/rXc/GQVaYpyDdyxK+ecHPVYJSN9bmVFBvjA/9eOB+pb3F2w2N6fc5qB9Ew5yIns" crossorigin="anonymous">	
	
	<!-- Styles -->
	<!-- <link href="{{ asset('css/plugins/bootstrap.min.css', env('HTTPS', NULL)) }}" rel="stylesheet"> -->
	<link href="{{ asset('css/plugins/font-awesome/css/font-awesome.min.css', env('HTTPS', NULL)) }}" rel="stylesheet">
	<link href="{{ asset('css/style.css', env('HTTPS', NULL)) }}" rel="stylesheet">
	<!-- <script src="{{ asset('js/plugins/jquery-3.3.1.min.js') }}"></script> -->
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
	<!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss/dist/tailwind.min.css" rel="stylesheet"> -->

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
		<router-view></router-view>
	</div>
	<!-- Scripts -->
	<script src="{{ asset('js/app.js', env('HTTPS', NULL)) }}"></script>
	
	 <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>
</html>
