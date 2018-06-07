@extends('layouts.app')

@section('content')
	<link href="{{ asset('css/judge.css') }}" rel="stylesheet">
	<div class="container mt-2">
		<div class="row">
			<div class="col-12">
				<div class="container py-1 header-information">
					<div class="row">
						<div class="col-12 col-md-5 text-center">
							<img src="{{ $judge->photo }}" alt="фото" class="mr-3">
						</div>
						<div class="col-12 col-md-7 mt-3  text-center text-md-left">
							<h2 class="font-weight-bold">
								{{ $judge->surname }}
								@if (strlen($judge->name) > 2)
									{{ $judge->name }} {{ $judge->patronymic }}
								@else
									{{ $judge->name }}. {{ $judge->patronymic }}.
								@endif
							</h2>
							<h3 class="text-muted"><i class="fa fa-university" aria-hidden="true"></i> {{ $judge->court_name }}</h3>
							@if($judge->court_address)
								<div class="text-muted">&nbsp;<i class="fa fa-map-marker"> &nbsp; </i> {{ $judge->court_address }}</div>
							@endif
							@if($judge->court_phone)
								<div class="text-muted">&nbsp;<i class="fa fa-phone"> &nbsp; </i> {{ $judge->court_phone }}</div>
							@endif
							@if($judge->court_email)
								<div class="text-muted">&nbsp;<i class="fa fa-envelope-o"> &nbsp; </i> {{ $judge->court_email }}</div>
							@endif
							@if($judge->court_site)
								<div class="text-muted">&nbsp;<a href="{{ $judge->court_site }}" target="_blank"><i class="fa fa-link"> &nbsp; </i> {{ $judge->court_site }}</a></div>
							@endif
							<hr class="mt-0">
							<span id="judge{{ $judge->id }}">
								@include('judges.judge-statuses')
							</span>
								{{--if юзер ввійшов - є можливість змінювати статус, додавати в закладки--}}
							@if(Auth::check())
									<i class="fa fa-pencil p-1" aria-hidden="true"  data-toggle="modal" data-target="#changeJudgeStatus"></i>
									<small class="text-muted float-right mr-3 bookmark" onclick="addBookmark(this, {{ $judge->id }})">
									@if($judge->is_bookmark == 1)
										<span>відстежується</span> <i class="fa fa-bookmark" aria-hidden="true"></i>
									@else
										<span>відстежувати</span> <i class="fa fa-bookmark-o" aria-hidden="true"></i>
									@endif
								</small>
							@endif
							<div class="mt-4 text-center">
								<span class="likes-unlikes">
									@include('judges.judge-likes-unlikes')
								</span>
								<i class="fa fa-line-chart mx-5" aria-hidden="true" title="рейтинг"> NaN </i>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	
	<div class="container mt-1" id="judge-statistic">
		<div class="row justify-content-start">
			<div class="col-12 col-xl-6 mb-4 pr-xl-1">
				<div class="card card-outline-secondary">
					<div class="card-header">
						<h5>Статистика розглянутих справ</h5>
					</div>
					<div class="card-body">
						<div id="common-statistic"></div>
					</div>
				</div>
			</div>
			<div class="col-12 col-xl-6 mb-4 pl-xl-1">
				<div class="card card-outline-secondary">
					<div class="card-header">
						<h5>Ефективність</h5>
					</div>
					<div class="card-body">
						<div class="text-center in-develop">
							<i class="fa fa-code" aria-hidden="true"></i> <br>в розробці
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-12 col-xl-6 pr-xl-1">
				<div class="card card-outline-secondary mx-0">
					<div class="card-header">
						<h5>Цивільне судочинство</h5>
					</div>
					<div class="card-body">
						<div class="text-center in-develop">
							<i class="fa fa-code" aria-hidden="true"></i> <br>в розробці
						</div>
					</div>
				</div>
			</div>
			<div class="col-12 col-xl-6 pl-xl-1">
				<div class="card card-outline-secondary mx-0">
					<div class="card-header">
						<h5>Кримінальне судочинство</h5>
					</div>
					<div class="card-body">
						<div class="text-center in-develop">
							<i class="fa fa-code" aria-hidden="true"></i> <br>в розробці
						</div>
					</div>
				</div>
			</div>
			
		</div>
	</div>
	
	
	@if(Auth::check())
		<!-- Modal -->
		<div class="modal fade" id="changeJudgeStatus" tabindex="-1" role="dialog" aria-labelledby="changeJudgeStatusLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="changeJudgeStatusLabel">Оновити статус судді</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<div class="form-group row mt-1">
								<label for="chooser-judge-status" class="col-2 col-form-label">Статус</label>
								<select class="form-control col-8 ml-4" id="chooser-judge-status">
									<option value="1">&#xf0b1; &nbsp; на роботі</option>
									<option value="2">&#xf0fa; &nbsp; на лікарняному</option>
									<option value="3">&#xf274; &nbsp; у відпустці</option>
									<option value="4">&#xf272; &nbsp; відсутній на робочому місці</option>
									<option value="5">&#xf273; &nbsp; припиено повноваження</option>
								</select>
							</div>
							<div class="form-group row mt-1">
								<label for="status-end-date" class="col-6 col-form-label">Дата завершення дії статусу <br><sup class="text-muted">(якщо відома)</sup></label>
								<div class="col-5">
									<input class="form-control" type="date" min="{{ date('Y-m-d') }}" id="status-end-date">
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Закрити</button>
						<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="updateJudgeStatus({{ $judge->id }})">Змінити статус</button>
					</div>
				</div>
			</div>
		</div>
		<script>
			var statisticData = @json($statistic);
		</script>
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script src="{{ asset('js/judge.js') }}"></script>
	@endif

@endsection
