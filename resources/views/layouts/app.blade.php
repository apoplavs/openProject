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
	<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
	<link href="{{ asset('css/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
	<link href="{{ asset('css/style.css') }}" rel="stylesheet">
	<script src="{{ asset('js/jquery-3.3.1.min.js') }}"></script>
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
	<!-- <div id="app"> -->
		<nav class="navbar navbar-expand-lg navbar-light bg-light z-depth-3">
			<a href="{{ url('/') }}" class="navbar-brand p-0 m-0">
				<img src="{{ asset('img/logo.png') }}" width="40" alt="logo">
				{{ config('app.name', 'ТОЕсуд') }}
			</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse justify-content-end" id="navbarSupportedContent">
				<!-- <form class="form-inline my-2 my-md-0">
					<input class="form-control" type="text" placeholder="Пошук">
					<button class="btn btn-outline-success m-2 my-sm-0">Пошук</button>
				</form> -->
				<ul class="navbar-nav" id="top-navbar">
					<li class="nav-item active">
						<a class="nav-link" href="{{ url('/') }}">На головну<span class="sr-only">(поточна)</span></a>
					</li>
					<li class="nav-item dropdown active">
						<a class="nav-link dropdown-toggle" href="#" id="dropdown06" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						Рейтинг
						</a>
						<div class="dropdown-menu" aria-labelledby="dropdown06">
						  <a class="dropdown-item" href="{{ url('/judges') }}">Судді</a>
						  <a class="dropdown-item disabled" href="#">Суди <small>( <i class="fa fa-code" aria-hidden="true"></i> в розробці)</small></a>
						</div>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="{{ url('about') }}">Про нас</a>
					</li>
					<li class="nav-item">
						<a class="nav-link" href="#">Контакти</a>
					</li>
				  	
					<!-- Authentication Links -->
					<div class="mx-5"></div>
					@guest
					<li class="nav-item active">
						<a class="nav-link" href="{{ route('login') }}">Вхід</a>
					</li>
					<li class="nav-item active">
						<a class="nav-link" href="{{ route('register') }}">Рєстрація</a>
					</li>
					@else
					<li class="nav-item dropdown pr-4 mr-5 active">
						<a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" id="dropdown-menu" role="button" aria-expanded="false" aria-haspopup="true" v-pre>
						{{ Auth::user()->name }} <span class="caret"></span>
						</a>
						<!-- <div class="dropdown-menu" aria-labelledby="dropdown-menu">
						  <a class="dropdown-item" href="#">Action</a>
						  <a class="dropdown-item" href="#">Another action</a>
						  <a class="dropdown-item" href="#">Something else here</a>
						</div> -->
						<ul class="dropdown-menu dropdown-menu-right px-1">
							<li>
								<span class="text-muted">
									{{ Auth::user()->email }}
								</span>
							</li>
							
							<hr class="my-2">
							
							<a href="{{ url('/home') }}">
								<li>
									Особистий кабінет <i class="fa fa-home text-muted float-right" aria-hidden="true"></i>
								</li>
							</a>
							
							<hr class="my-2">
							
							<a href="{{ route('settings') }}">
								<li>
									Налаштування <i class="fa fa-cog text-muted float-right" aria-hidden="true"></i>
								</li>
							</a>
							
							<hr class="my-2">
							
							<li>
								<a href="{{ route('logout') }}"
								   onclick="event.preventDefault();
											 document.getElementById('logout-form').submit();">
									Вийти <i class="fa fa-sign-out text-muted float-right" aria-hidden="true"></i>
								</a>
								<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
									{{ csrf_field() }}
								</form>
							</li>
						</ul>
					</li>
					@endguest
				</ul>
			</div>
		</nav>
	
	 {{--preloader--}}
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
		<div id="loader"></div>
	
<div id="content" class="blurry">
	@yield('content')
</div>
	

	<!-- Scripts -->
{{--    <script src="{{ asset('js/app.js') }}"></script>--}}
	
{{--	<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>--}}
	<script src="{{ asset('js/bootstrap.min.js') }}"></script>
	<script src="{{ asset('js/main.js') }}"></script>
	{{--<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>--}}
</body>
</html>
