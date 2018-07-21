@extends('layouts.app')

@section('content')
	<link href="{{ asset('css/home.css') }}" rel="stylesheet">
@if(!empty($judges_list))
	<div class="container mr-5 mt-4" id="follow-judges">
		<div class="row mb-1">
			<div class="col-12">
				<div class="card card-outline-secondary my-0">
					<div class="card-header">
						<i class="fa fa-bookmark mr-1" aria-hidden="true"></i> Судді що відстежуються
					</div>
				</div>
			</div>
		</div>
		
		
		<div class="row justify-content-start">
			@foreach($judges_list as $judge)
			<div class="col-12 col-md-6 col-lg-4 col-xl-3">
				<div class="container mb-2 py-2">
					<a href="{{ url('/judges/'. $judge->id) }}">
						<div class="row text-center">
							<div class="col-12 px-0" title="ПІБ судді">
								{{ $judge->surname }}
									@if (strlen($judge->name) > 2)
										{{ $judge->name }} {{ $judge->patronymic }}
									@else
										{{ $judge->name }}. {{ $judge->patronymic }}.
									@endif
							</div>
						</div>
						<div class="row text-center">
							<div class="col-12 mx-0">
								<img src="{{ $judge->photo }}" alt="фото">
							</div>
						</div>
					</a>
					<div class="row text-center">
						<div class="col-12 my-1 court-name" title="найменування суду">
							{{ $judge->court_name }}
						</div>
					</div>
					<div class="row">
						{{--<div class="col-4" title="рейтинг">--}}
							{{--<i class="fa fa-line-chart" aria-hidden="true"> NaN</i>--}}
						{{--</div>--}}
						<div class="col-12 pr-0">
							<small class="text-muted" id="judge{{ $judge->id }}" title="статус судді, дата оновлення статусу">
								@include('judges.judge-statuses')
							</small>
							<span title="змінити статус судді"><i class="fa fa-pencil p-1" aria-hidden="true"  data-toggle="modal" data-target="#changeJudgeStatus"
									 onclick="setJudgeToChangeStatus({{ $judge->id }})"></i></span>
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
	</div>
@endif
	
	<div class="container mr-5">
		<div class="row">
			<!-- Main list -->
			<div class="col-12">
				<div class="card card-outline-secondary my-4">
					<div class="card-header">
						<i class="fa fa-history" aria-hidden="true"></i> Історія переглядів
					</div>
					<div class="card-body p-2" id="user-history">
						<hr class="mt-0 mb-1">
						{{--якщо історії переглядів немає--}}
						@if(empty($judges_history) || empty($judges_history[0]))
							<div>пусто</div>
						@else
						<div class="container">
							@foreach($judges_history as $judge)
								<div class="row">
									<div class="col-12 col-lg-2 p-0 text-center">
										<img src="{{ $judge->photo }}" alt="фото">
									</div>
									<div class="col-12 col-lg-10 p-0">
										<div class="container">
											<div class="row">
												<div class="col-12 col-lg-6 text-center text-lg-left" title="ПІБ судді">
													<h5>
														<a href="{{ url('/judges/'. $judge->id) }}">{{ $judge->surname }}
															@if (strlen($judge->name) > 2)
																{{ $judge->name }} {{ $judge->patronymic }}
															@else
																{{ $judge->name }}. {{ $judge->patronymic }}.
															@endif
														</a>
													</h5>
												</div>
												<div class="col-4 col-lg-2" title="рейтинг">
													<i class="fa fa-line-chart mx-3" aria-hidden="true"> NaN </i>
												</div>
												<div class="col-8 col-lg-4">
												<span class="text-muted">
													@if(Auth::check())
														<small class="text-muted float-right mr-3 bookmark" onclick="addBookmark(this, {{ $judge->id }})">
														{{--<input type="hidden" value="{{ $judge->id }}">--}}
															@if($judge->is_bookmark == 1)
																<span>відстежується</span> <i class="fa fa-bookmark" aria-hidden="true"></i>
															@else
																<span>відстежувати</span> <i class="fa fa-bookmark-o" aria-hidden="true"></i>
															@endif
													</small>
													@endif
												</span>
												</div>
											</div>
											<div class="row">
												<div class="col-12 col-lg-7" title="найменування суду">
													<div class="float-left court-name">{{ $judge->court_name }}</div>
												</div>
												<div class="col-12 col-lg-5">
													<small class="text-muted ml-1" id="judge{{ $judge->id }}" title="статус судді, дата оновлення статусу">
														@include('judges.judge-statuses')
													</small>
													{{--if юзер ввійшов - є можливість змінювати статус--}}
													@if(Auth::check())
														<span title="змінити статус судді"><i class="fa fa-pencil p-1" aria-hidden="true"  data-toggle="modal" data-target="#changeJudgeStatus"
																							  onclick="setJudgeToChangeStatus({{ $judge->id }})"></i></span>
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
								<hr class="my-3 my-lg-2">
							@endforeach
						</div> <!-- container -->
						@endif
					</div> <!-- card-body -->
				</div> <!-- card-outline-secondary -->
			</div> <!-- col-12 -->
		</div> <!-- row -->
	</div> <!-- container -->
	
	
	
	
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
							<input type="hidden" id="judge-for-new-status" value="0">
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
					<button type="button" class="btn btn-primary" data-dismiss="modal" onclick="updateJudgeStatus()">Змінити статус</button>
				</div>
			</div>
		</div>
	</div>

@endsection
